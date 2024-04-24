<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 25/03/2023
 * Time: 18:04
 */

namespace App\Actions;

use App\AI\Art\DalE3;
use App\AI\Art\ReplicateInstantId;
use App\AI\Chat\ChatConversationInterface;
use App\AI\Prompts\RawPrompt;
use App\Enums\BookStatuses;
use App\Events\BookFailed;
use App\Events\BookWritten;
use App\Jobs\GenerateImage;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Image;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use JsonException;
use Log;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Str;
use Throwable;

class GenerateBook
{
    public function handle(Book $book): Book
    {
        $conv = app(ChatConversationInterface::class);

        $book->status = BookStatuses::GeneratingText;
        $book->forceFill(["additional_data->chatModel" => $conv->getModel()]);
        $book->forceFill(["additional_data->artModel"  => $book->additional_data["request"]["character"] ?? null ? ReplicateInstantId::class : DalE3::class]);
        $book->save();

        $conv->addSystemMessage(new RawPrompt($this->getSystemMessage($book, $conv)));
        $message = $conv->send(new RawPrompt($book->input));

        try {
            $bookResponse = json_decode($this->getJsonFromMessage($message), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new \RuntimeException("Malformed response from openAI");
        }

        Log::debug("[GenerateBook][handle] Received bookResponse", $bookResponse);

        if ($bookResponse["error_message"] ?? null) {
            Log::debug("[GenerateBook][handle] Got error from provider: {$bookResponse["error_message"]}", $bookResponse);
            $book->status = BookStatuses::FailedText;
            $book->fill(["additional_data->error" => $bookResponse]);
            $book->save();
            throw new \RuntimeException("GenerateBook: " . json_encode($bookResponse, true));
        }

        for ($i = 2; $i <= 4 && false; $i++) {
            $prompt = config("prompts." . ($i == 4 ? "last_following_chapters" : "following_chapters"));
            $message = $conv->send(new RawPrompt($prompt));

            try {
                $additionalResponse = json_decode($this->getJsonFromMessage($message), true, flags: JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                throw new \RuntimeException("Malformed response from openAI");
            }

            $bookResponse["chapters"] = array_merge(
                $bookResponse["chapters"],
                $additionalResponse["chapters"]
            );
        }

        return $this->fromArray($book, $bookResponse, $conv);
    }

    public function fromArray(Book $book, array $data, ChatConversationInterface $conv): Book
    {
        \Log::debug("[GenerateBook][fromArray] Got Request: " . json_encode($data, JSON_UNESCAPED_UNICODE));
        $data = $this->trimArrayKeys($data);

        // Create or find the author
        $data['tags'] = implode(",", $data['tags']);

        // Create the book
        $book->status = BookStatuses::GeneratingImages;
        $lang = $book->additional_data["request"]["language"] ?? "he";
        $n = new Niqqud();
        $tr = new GoogleTranslate($lang, 'en');
        if ($lang != "en") {
            $bookTranslated = [$data["title"], $data["description"], $data["tags"]];
            if (preg_match('/[\x{0590}-\x{05FF}]/u', $data["description"]) === 0) {
                $bookTranslated = explode("#####", $tr->translate(join("\n#####\n", $bookTranslated)));
            }
            $data["title"] = trim($bookTranslated[0]);
            $data["description"] = trim($bookTranslated[1]);
            $data["tags"] = trim($bookTranslated[2]);
            if ($lang == "he") {
                $data["title"] = $n->handle($data["title"]);
                $data["description"] = $n->handle($data["description"]);
                $data["tags"] = $n->handle($data["tags"]);
            }
        }
        $book->fill(Arr::only($data, ['title', 'description', 'cover_image', 'tags', 'rating']));
        $book->fill([
            "additional_data->chatGPTUsages" => $conv->getUsages(),
            "additional_data->costs_usd" => $conv->getUsages()->sum(fn($usage) => $usage['prompt_cost'] + $usage['completion_cost']),
        ]);
        $book->save();

        // Create chapters and related images
        $chaptersData = Arr::get($data, 'chapters', []);
        $images = Collection::make();
        foreach ($chaptersData as $i => $chapterData) {
            Log::debug("[GenerateBook][fromArray] Starting With chapter " . ($i + 1));

            if ($lang != "en") {
                Log::debug("[GenerateBook][fromArray] Got [{$chapterData["title"]}] {$chapterData["content"]}");
                $chapterTranslated = [$chapterData["title"], $chapterData["content"]];

                if (preg_match('/[\x{0590}-\x{05FF}]/u', $chapterData["content"]) === 0) {
                    $chapterTranslated = explode("#####", $tr->translate(join("\n#####\n", $chapterTranslated)));
                    Log::debug("[GenerateBook][fromArray] Translation response [{$chapterTranslated[0]}] {$chapterTranslated[1]}");
                }
                $chapterData["title"] = trim($chapterTranslated[0]);
                $chapterData["content"] = trim($chapterTranslated[1]);
                if ($lang == "he") {
                    $chapterData["title"] = $n->handle($chapterData["title"]);
                    $chapterData["content"] = $n->handle($chapterData["content"]);
                }
            }

            $chapter = new Chapter(Arr::only($chapterData, ['number', 'title', 'content']));
            $chapter->number = $i + 1;
            $chapter->book_id = $book->id;
            $chapter->save();


            $image = new Image();
            $image->book_id = $book->id;
            $image->chapter_id = $chapter->id;
            $image->prompt = rtrim($chapterData["illustrator_instructions_prompt"] ?? null , ".") . ". {$data["art"]}";
            $image->save();

            $images->add($image);

            Log::debug("Queueing GenerateImage for image {$image->id}");
        }

        Bus::batch($images->map(fn(Image $image) => new GenerateImage($image)))
            ->name("GenerateImage book {$book->id}")
            ->then(function (Batch $batch) use ($book) {
                (new CheckCompleteBook())->execute($book);
            })->catch(function (Batch $batch, Throwable $e) use ($book) {
                $book->status = BookStatuses::FailedImages;
                $book->save();
                event(new BookFailed($book));
            })
            ->dispatch();

        event(new BookWritten($book));

        return $book;
    }


    /**
     * @param $content
     *
     * @return string
     */
    protected function getJsonFromMessage($content): string
    {
        return "{".Str::of($content)
                  ->after("{")
                  ->beforeLast("}") . "}";
    }

    protected function getSystemMessage(Book $book, ChatConversationInterface $conv): string
    {
        $request = $book->additional_data["request"];
        $chapters = match ((int)$request["isAdultReader"] . "|" . $request["age"]) {
            "0|3-5"  => ["sentences" => "1-3",   "pages" => "5-7"],
            "0|6-8"  => ["sentences" => "5-10",  "pages" => "7-10"],
            "0|9-12" => ["sentences" => "11-15", "pages" => "11-15"],
            "1|3-5"  => ["sentences" => "3-6",   "pages" => "5-7"],
            "1|6-8"  => ["sentences" => "5-8",   "pages" => "7-10"],
            "1|9-12" => ["sentences" => "7-15",  "pages" => "11-15"],
        };

        $replacements = [
            ":Language:"             => match ($conv->isSupportedLanguage($request["language"]) ? $request["language"] : "en") {
                "en" => "English",
                "he" => "Hebrew",
            },
            ":MainMoral:"            => $request["moral"],
            ":ArtStyle:"             => match ($request["art-style"] ?? null) {
                "random", null  => Arr::random(["Walt Disney", "Anime", "Dreamworks", "Pixar"]),
                "asked-in-text" => "as user describe in input",
                default         => $request["art-style"]
            },
            ":SentencesInPageRange:" => $chapters["sentences"],
            ":PagesRange:"           => $chapters["pages"],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), config("prompts.generate-tale"));
    }

    private function trimArrayKeys(array $input): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            $result[trim($key)] = is_array($value) ? $this->trimArrayKeys($value) : $value;
        }

        return $result;
    }
}
