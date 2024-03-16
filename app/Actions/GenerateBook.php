<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 25/03/2023
 * Time: 18:04
 */

namespace App\Actions;

use App\AI\Chat\ChatConversationInterface;
use App\AI\Prompts\RawPrompt;
use App\Enums\BookStatuses;
use App\Events\BookCompleted;
use App\Events\BookFailed;
use App\Events\BookWritten;
use App\Jobs\GenerateImage;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Image;
use App\Models\Placeholder;
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
            $image->prompt = $chapterData["illustrator_instructions_prompt"] ?? null;
            $image->save();

            $images->add($image);

            Log::debug("Queueing GenerateImage for image {$image->id}");
        }

        Bus::batch($images->map(fn(Image $image) => new GenerateImage($image)))
            ->name("GenerateImage book {$book->id}")
            ->then(function (Batch $batch) use ($book) {
                $book->status = BookStatuses::Ready;
                $book->save();
                event(new BookCompleted($book));
            })->catch(function (Batch $batch, Throwable $e) use ($book) {
                $book->status = BookStatuses::FailedImages;
                $book->save();
                event(new BookFailed($book));
            })
            ->dispatch();

        $placeholdersData = Arr::get($data, 'placeholders', []);
        foreach ($placeholdersData as $placeholderData) {
            $placeholder = new Placeholder($placeholderData);
            $placeholder->book_id = $book->id;
            $placeholder->save();
        }

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

    /**
     * @return string
     */
    protected function getPrompt(): string
    {
        return '{
  "title": "The Adventure Camp by the Stream",
  "description": "Join a group of 8-year-old children as they build a camp by the stream next to their house.",
  "tags": ["children", "adventure", "camp", "friendship", "creativity"],
  "chapters": [
    {
      "title": "Chapter One: The Discovery",
      "content": "Once upon a time, in a small settlement nestled in the peaceful lowlands, lived a group of adventurous 8-year-old children - Mia, Ben, Lily, and Alex. They were the best of friends and loved spending time together. One sunny day, as they were playing near their houses, they heard the sound of rushing water.\n\nCuriosity sparked within them, and they followed the sound until they reached a beautiful stream gurgling beside Mia\'s house.\n\nExcitedly, the children decided to build a camp right next to the stream. They envisioned a magical place where they could have adventures, learn new things, and have lots of fun.\n\nWith determination in their hearts, they set off to work.",
      "illustrator_instructions_prompt": "Illustrate the children discovering the stream and planning their camp."
    },
    {
      "title": "Chapter Two: Teamwork and Creativity",
      "content": "Mia, being the natural leader, came up with a brilliant plan for their camp. She assigned tasks to each of her friends. Ben was responsible for gathering branches to build a sturdy shelter. Lily was in charge of finding smooth stones for decorating their campfire area, and Alex took charge of collecting colorful wildflowers to brighten up the camp.\n\nUnder the bright blue sky, the children worked together, their laughter echoing through the trees. They used their creativity and imagination to transform the streamside into a magical campsite.\n\nMia used the branches to construct a cozy shelter, while Ben and Lily arranged the smooth stones in a circle, creating a perfect spot for their campfire. Alex carefully placed the wildflowers around the camp, adding a touch of beauty to their new space.",
      "illustrator_instructions_prompt": "Illustrate the children working together, using their creativity to build their camp."
    },
    {
      "title": "Chapter Three: Exploring Nature\'s Wonders",
      "content": "With the camp complete, the children were ready to embark on their first adventure. They decided to explore nature\'s wonders surrounding their campsite.\n\nThey wandered through the dense forest, marveling at the towering trees and discovering colorful mushrooms and curious insects along the way. They learned about different plants and animals, developing a deep appreciation for the natural world.\n\nAs they reached the stream, they were fascinated by the sparkling water and the abundance of fish swimming downstream. They spent hours observing the water, mesmerized by its soothing sounds and magical reflections.",
      "illustrator_instructions_prompt": "Illustrate the children exploring nature and observing the stream."
    },
    {
      "title": "Chapter Four: Friendship and Adventure",
      "content": "The camp by the stream became a gathering place for the children and their friends from the settlement. They shared stories, played games, and learned together. The camp became a symbol of their friendship and love for nature.\n\nOne evening, as the sun began to set, Mia had an idea. She suggested going on an exciting adventure to find the hidden treasure rumored to be hidden deep in the nearby woods. The children\'s eyes lit up with excitement, and they eagerly agreed to the quest.\n\nHand in hand, they ventured into the unknown, supporting and encouraging one another along the way. Although they didn\'t find any treasure, they discovered something even more valuable - the power of friendship and the courage to explore the world around them.",
      "illustrator_instructions_prompt": "Illustrate the children embarking on their adventure, hand in hand, with smiles on their faces."
    },
    {
      "title": "Chapter Five: Cherishing Memories",
      "content": "As time passed, the camp by the stream held a special place in the children\'s hearts. They cherished the memories they shared and the lessons they learned. Each time they returned to the camp, they discovered something new, whether it was a bird\'s nest nestled in a tree or a secret wildflower blooming nearby.\n\nThe children\'s creativity, teamwork, and curiosity were nurtured by their time spent in the camp. They grew into brave and confident individuals, always ready for new adventures.\n\nAnd so, with hearts full of gratitude and joy, the children continued to visit their camp by the stream, only to discover that the real treasure was the bond they had forged and the love they shared.",
      "illustrator_instructions_prompt": "Illustrate the children sitting together, reminiscing and cherishing the memories they made at the camp."
    }
  ]
}';
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
