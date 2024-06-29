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
use App\AI\GenerateAIStatuses;
use App\AI\Prompts\GenerateBookSystemPrompt;
use App\AI\Prompts\RawPrompt;
use App\Enums\BookStatuses;
use App\Events\BookFailed;
use App\Events\BookWritten;
use App\Jobs\GenerateImage;
use App\Jobs\TranslateBook;
use App\Jobs\TranslateChapter;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Character;
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
    public function __construct(protected ChatConversationInterface $chatConv) { }

    public function handle(Book $book): void
    {
        $book->status = BookStatuses::GeneratingText;
        $book->forceFill(["additional_data->chatModel" => $this->chatConv->getModel()]);
        $book->forceFill([
            "additional_data->artModel" => $book->additional_data["request"]["character"] ?? null ? ReplicateInstantId::class : DalE3::class
        ]);
        $book->save();

        $this->chatConv->addSystemMessage(new RawPrompt($this->getSystemMessage($book)));
        $this->chatConv->setId("generate_book:" . $book->id);

        $result = $this->chatConv->send(new RawPrompt($book->input));

        match ($result->status) {
            GenerateAIStatuses::Failed => throw new \RuntimeException("Malformed response from openAI"),
            GenerateAIStatuses::Completed => $this->completeBook($book, $result->message),

            default => null // Nothing
        };
    }

    public function completeBook(Book $book, string $message): Book
    {
        try {
            $bookResponse = json_decode($this->getJsonFromMessage($message), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new \RuntimeException("Malformed response from AI Provider");
        }

        Log::debug("[GenerateBook][completeBook] Received bookResponse", $bookResponse);

        if ($bookResponse["error_message"] ?? null) {
            Log::debug("[GenerateBook][handle] Got error from provider: {$bookResponse["error_message"]}", $bookResponse);
            $book->status = BookStatuses::FailedText;
            $book->fill(["additional_data->error" => $bookResponse]);
            $book->save();
            throw new \RuntimeException("GenerateBook: " . json_encode($bookResponse, true));
        }

        return $this->fromArray($book, $bookResponse);
    }

    public function fromArray(Book $book, array $data): Book
    {
        \Log::debug("[GenerateBook][fromArray] Got Request: " . json_encode($data, JSON_UNESCAPED_UNICODE));
        $data = $this->trimArrayKeys($data);

        // Create or find the author
        $data['tags'] = implode(",", $data['tags']);

        // Create the book
        $book->status = BookStatuses::GeneratingImages;
        $book->fill(Arr::only($data, ['title', 'description', 'cover_image', 'tags', 'rating']));
        $book->fill([
            "additional_data->chatGPTUsages" => $this->chatConv->getUsages(),
            "additional_data->costs_usd" => $this->chatConv->getUsages()->sum(fn($usage) => $usage['prompt_cost'] + $usage['completion_cost']),
        ]);
        $book->save();


        $lang = $book->additional_data["request"]["language"] ?? "he";
        $n = new Niqqud();
        $tr = new GoogleTranslate($lang, 'en');
        if ($lang != "en") {
            dispatch(new TranslateBook($book));
        }

        // Create chapters and related images
        $chaptersData = Arr::get($data, 'chapters', []);
        $images = Collection::make();
        foreach ($chaptersData as $i => $chapterData) {
            Log::debug("[GenerateBook][fromArray] Starting With chapter " . ($i + 1) . " Got [{$chapterData["title"]}] {$chapterData["content"]}");

            $chapter = new Chapter(Arr::only($chapterData, ['number', 'title', 'content']));
            $chapter->number = $i + 1;
            $chapter->book_id = $book->id;
            $chapter->save();

            if ($lang != "en") {
                dispatch(new TranslateChapter($chapter));
            }

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

    protected function getSystemMessage(Book $book): string
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

        $characterId = $book->additional_data["request"]["character"] ?? null;
        $character = $characterId ? Character::find($characterId) : null;

        return new GenerateBookSystemPrompt(
            match ($this->chatConv->isSupportedLanguage($request["language"]) ? $request["language"] : "en") {
                "en" => "English",
                "he" => "Hebrew",
            },
            match ($request["art-style"] ?? null) {
                "random", null  => Arr::random(["Walt Disney", "Anime", "Dreamworks", "Pixar"]),
                "asked-in-text" => "as user describe in input",
                default         => $request["art-style"]
            },
            $request["moral"],
            $request["age"],
            $character,
            $chapters["sentences"],
            $chapters["pages"]
        );
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
