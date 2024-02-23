<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 25/03/2023
 * Time: 18:04
 */

namespace App\Actions;

use App\Enums\BookStatuses;
use App\Jobs\GenerateImage;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Image;
use App\Models\Placeholder;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Log;
use OpenAI\Contracts\ClientContract;
use OpenAI\Laravel\Facades\OpenAI;
use Str;
use Throwable;

class GenerateBook
{

    const MODEL_GPT_4_0125_PREVIEW = 'gpt-4-0125-preview';
    const MODEL_GPT_3_5_TURBO = 'gpt-3.5-turbo';

    protected array $messages = [];
    protected array $usages = [];

    protected string $currentModel = self::MODEL_GPT_4_0125_PREVIEW;
    protected static array $aiModels = [
        self::MODEL_GPT_4_0125_PREVIEW => ["costsPer1K" => ["prompt" => 0.0100, "completion" => 0.0300]],
        self::MODEL_GPT_3_5_TURBO      => ["costsPer1K" => ["prompt" => 0.0005, "completion" => 0.0015]],
    ];

    public function handle(Book $book): Book
    {
        $book->status = BookStatuses::GeneratingText;
        $book->save();

        $this->messages = [
            ['role' => 'system', 'content' => $this->getSystemMessage($book)],
        ];

        $message = $this->generateMessage($book->input);

        $bookRequest = json_decode($this->getJsonFromMessage($message["content"]), true);
        Log::debug("[GenerateBook][handle] Received bookRequest", $bookRequest);

        for ($i = 2; $i <= 4 && false; $i++) {
            $prompt = config("prompts." . ($i == 4 ? "last_following_chapters" : "following_chapters"));
            $message = $this->generateMessage($prompt);

            $message["content"] = $this->getJsonFromMessage($message["content"]);

            $bookRequest["chapters"] = array_merge(
                $bookRequest["chapters"],
                json_decode($message["content"], true)["chapters"]
            );
        }

        return $this->fromArray($book, $bookRequest);
    }

    public function fromArray(Book $book, array $data): Book
    {
        \Log::debug("[fromArray] Got Request: " . json_encode($data, JSON_UNESCAPED_UNICODE));

        // Create or find the author
        $data['tags'] = implode(",", $data['tags']);

        // Create the book
        $book->status = BookStatuses::GeneratingImages;
        $book->fill(Arr::only($data, ['title', 'description', 'cover_image', 'tags', 'rating']));
        $book->fill([
            "additional_data->chatGPTUsages" => $this->usages,
            "additional_data->costs_usd" => collect($this->usages)->sum(fn($usage) => $usage['prompt_cost'] + $usage['completion_cost']),
        ]);
        $book->save();

        // Create chapters and related images
        $chaptersData = Arr::get($data, 'chapters', []);
        $images = Collection::make();
        foreach ($chaptersData as $i => $chapterData) {
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
            })->catch(function (Batch $batch, Throwable $e) use ($book) {
                $book->status = BookStatuses::FailedImages;
                $book->save();
            })
            ->dispatch();

        $placeholdersData = Arr::get($data, 'placeholders', []);
        foreach ($placeholdersData as $placeholderData) {
            $placeholder = new Placeholder($placeholderData);
            $placeholder->book_id = $book->id;
            $placeholder->save();
        }

        return $book;
    }

    public static function aaa()
    {
        $x = 'Omer, a software programmer in PayMe, a startup company in payments industry. he is a fan Maccabi Haifa and riding a skateboard. He is a friend of Gilad and Ohad. Gilad is a funny standup comedian (and not so fat in second look) and Ohad is a fast talker, have 3 child and a little arab. It is required to come up with the names of the parents and to remain consistent during the story. The group studies in the same class and meets every afternoon at the playground on the street where they live in Tel Aviv.';
        (new static())->handle($x);
    }

    public static function bbb()
    {
        $x = '{"title":"Danny and the Big Game","description":"Danny and his friends go on an adventure to save their soccer game.","tags":["children","storybook"],"chapters":[{"title":"Chapter One: The Soccer Game","content":"Danny and his friends Ido and Tomer are on their way to their big soccer game against a rival team. Danny can\'t wait to play and show off his skills on the field. As they walk to the field, Danny spots a skateboard on the sidewalk. Without thinking, he picks it up and starts riding it down the street, showing off his cool tricks to his friends. Suddenly, he loses his balance and falls off, scraping his knee. Danny starts to cry, but Ido and Tomer help him up and encourage him to keep going.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny, Ido, and Tomer walking down the street with their soccer gear.","caption":"Danny and his friends on their way to their soccer game."},{"illustrator_instructions_prompt":"\/imagine Draw Danny falling off his skateboard and scraping his knee.","caption":"Danny falls off his skateboard and needs help from his friends."}]},{"title":"Chapter Two: The Lost Soccer Ball","content":"When they arrive at the field, they realize that they forgot their soccer ball at the playground. Without it, they can\'t play the game. Danny feels guilty for losing the ball and begins to worry that they won\'t be able to find it in time. His friends reassure him that they will search every corner of the playground until they find it.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny, Ido, and Tomer searching for the lost soccer ball.","caption":"Danny and his friends search for the lost soccer ball."}]},{"title":"Chapter Three: The Adventure","content":"As they search for the soccer ball, they come across a group of younger kids who are playing with it. The kids refuse to give it back, starting a game of keep-away with the ball. Danny and his friends know they can\'t let this ruin their big game, so they come up with a plan to get the ball back. With Danny\'s skateboarding and Ido\'s soccer skills, they manage to outsmart the younger kids and retrieve their ball in time for the game.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny, Ido, and Tomer facing off against the younger kids for their soccer ball.","caption":"Danny and his friends work together to retrieve their soccer ball."}]},{"title":"Chapter Four: The Big Game","content":"With their soccer ball back and their confidence boosted, Danny and his friends are ready for the big game. They play their hearts out, scoring goal after goal and defending their side of the field with all they\'ve got. At the end of the game, they emerge victorious, ecstatic with their win. Danny realizes that even though they faced obstacles along the way, they were able to overcome them together with the help of his friends.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny, Ido, and Tomer celebrating their soccer win.","caption":"Danny and his friends celebrate their soccer victory."}]},{"title":"Chapter Five: The Family Argument","content":"After the big game, Danny comes home to his parents who are proud of him for winning. However, they notice the scrape on his knee and ask about it. Danny tells them about falling off his skateboard and his parents scold him for being reckless with his safety. Danny gets angry and argues with them, feeling like they don\'t understand him. After he calms down, Danny realizes that his parents were only worried about his safety and that he should listen to them more often.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny arguing with his parents.","caption":"Danny argues with his parents after the soccer game."}]},{"title":"Chapter Six: The Weepy Tomer","content":"One day at the playground, Tomer starts crying after a minor injury. Danny and Ido don\'t know how to comfort him and feel helpless. However, Danny remembers the words of his mom who once told him that it\'s important to be there for your friends when they need you. Danny and Ido sit with Tomer and listen to him, offering words of encouragement and comforting him. Tomer feels better and grateful for their support.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny and Ido comforting Tomer at the playground.","caption":"Danny and Ido help Tomer feel better at the playground."}]},{"title":"Chapter Seven: Danny\'s Skateboard Challenge","content":"Danny decides to take on a new challenge with his skateboard and participates in a local skateboarding competition. Despite feeling nervous, Danny practices hard every day and gets support from his friends and family. On the day of the competition, Danny does his best and impresses everyone with his tricks. And while he might not have won the competition, he realizes that it\'s the journey and the effort that matter the most.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny performing his skateboard tricks at the competition.","caption":"Danny participates in a skateboarding competition."}]},{"title":"Chapter Eight: The Friendship Chain","content":"Danny and his friends Ido and Tomer want to do something special for their classmate who\'s moving away. They come up with an idea to make a friendship chain made of paper dolls, where each person adds their own name to the chain. The three friends work on it together and add their own paper dolls to the chain. When they present it to the classmate, she is touched by their gesture and grateful for their friendship.","images":[{"illustrator_instructions_prompt":"\/imagine Draw the friendship chain made by Danny, Ido, and Tomer.","caption":"Danny and his friends make a friendship chain for their classmate."}]},{"title":"Chapter Nine: The Beach Day","content":"As the weather gets warmer, Danny and his friends decide to spend the day at the beach. They pack their lunches and bring their soccer ball and skateboard. However, once they get to the beach, they realize that they forgot sunscreen. Danny and Tomer both have sensitive skin and need it to avoid getting sunburned. They look around and see that there are no stores nearby. Danny takes charge and comes up with a solution - he remembers that his mom has some sunscreen in her purse, and the boys borrow it from her. The rest of the day at the beach is fun and sunburn-free.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny, Ido, and Tomer at the beach playing soccer and skateboarding.","caption":"Danny and his friends have fun at the beach."}]},{"title":"Chapter Ten: The Talent Show","content":"Danny\'s school is having a talent show and he wants to perform his skateboard tricks. However, he\'s nervous about performing in front of the whole school. His parents and friends encourage him to practice and work hard. Danny performs his routine at the talent show and the crowd goes wild - he\'s a hit! After the show, his friends congratulate him and tell him how proud they are.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny performing his skateboard tricks at the talent show.","caption":"Danny performs his skateboard tricks at the talent show."}]},{"title":"Chapter Eleven: The Bike Ride","content":"Danny\'s family is going on a bike ride and Danny looks forward to showing off his skills. However, his younger brother Ben is struggling to keep up and feels discouraged. Danny remembers how his friends helped him when he fell off his skateboard, and decides to ride next to his brother and encourage him to keep going. With Danny\'s support, Ben gains confidence and finishes the bike ride with a big smile on his face.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny and Ben riding their bikes together with Danny encouraging him.","caption":"Danny helps his brother Ben on their family bike ride."}]},{"title":"Chapter Twelve: The Science Fair","content":"Danny\'s class is having a science fair, and Danny wants to do an experiment related to skateboarding. He enlists the help of his friends in testing which surface is the best for skateboarding - asphalt, concrete, or wood. They work together on the experiment and present their findings at the science fair. Danny realizes that science can be fun and that he can combine his hobbies with learning.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny and his friends conducting their skateboard experiment.","caption":"Danny and his friends do a skateboard experiment for the science fair."}]},{"title":"Chapter Thirteen: The Giving Tree","content":"Danny and his friends notice that the tree outside their school is starting to wilt and needs some attention. They decide to take matters into their own hands and create a watering and care schedule for the tree, taking turns to make sure it gets what it needs. The tree soon becomes healthy and vibrant again, and the boys feel proud of their contribution to the environment.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny and his friends taking care of the school tree.","caption":"Danny and his friends take care of the tree outside their school."}]},{"title":"Chapter Fourteen: The Last Day of School","content":"As the school year comes to an end, Danny and his friends feel bittersweet. They\'ll miss spending every day together at school, but are excited for summer vacation. They exchange phone numbers and promises to stay in touch, and Danny feels grateful for such wonderful friends. He also reflects on how much he\'s grown and learned throughout the year - from being a good friend to taking care of the environment to trying new things. Danny is excited for what adventures the next school year will bring.","images":[{"illustrator_instructions_prompt":"\/imagine Draw Danny and his friends saying goodbye on the last day of school.","caption":"Danny and his friends say goodbye on the last day of school."}]}]}';
        (new static())->fromArray(json_decode($x, true));
    }

    /**
     * @param string $content
     *
     * @return array
     * @throws \Exception
     */
    protected function generateMessage(string $content): array
    {
        Log::info("[GenerateBook][generateMessage] generateMessage: {$content}");

        $this->messages[] = ['role' => 'user', 'content' => $content];

        if (Str::contains($content, "fake", true)) {
            Log::info("[GenerateBook][generateMessage] Fake!!!!");
            $this->usages[] = $this->enrichCosts(['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0]);
            sleep(2);

            return $this->messages[] = ['role' => 'assistant', 'content' => $this->getPrompt()];
        }

        $result = retry(1, function () {
            $result = app(ClientContract::class)->chat()->create([
                'model'    => $this->currentModel,
//            'model' => 'gpt-4',
                'messages' => $this->messages,
            ]);

            Log::debug("[GenerateBook][generateMessage] OpenAI response", $result->toArray());

            $this->usages[] = $this->enrichCosts($result->usage->toArray());

            Log::debug("[GenerateBook][generateMessage] OpenAI message", $result->choices[0]->message->toArray());

            if (!Str::isJson($this->getJsonFromMessage($result->choices[0]->message->content))) {
                throw new \RuntimeException("Malformed response from openAI");
            }

            return $result;
        });

        return $this->messages[] = $result['choices'][0]["message"];
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

    /**
     * @param $usage
     *
     * @return float[]
     */
    protected function enrichCosts($usage): array
    {
        $costs = self::$aiModels[$this->currentModel]['costsPer1K'];

        $usage['prompt_cost']     = ($costs['prompt'] * ($usage['prompt_tokens'] / 1000));
        $usage['completion_cost'] = ($costs['completion'] * ($usage['completion_tokens'] / 1000));

        return $usage;
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

        $replacements = [
            ":Language:"             => "Hebrew with Punctuation",
            ":MainMoral:"            => $book->additional_data["request"]["moral"],
            ":SentencesInPageRange:" => $chapters["sentences"],
            ":PagesRange:"           => $chapters["pages"],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), config("prompts.generate-tale"));
    }
}
