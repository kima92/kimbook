<?php

namespace App\Jobs;

use App\Actions\GenerateBook;
use App\Actions\Niqqud;
use App\Models\Book;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateBook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Book $book)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug("[TranslateBook][handle] Translating book {$this->book->id}");
        $lang = $this->book->additional_data["request"]["language"] ?? "he";

        $n = new Niqqud();
        $tr = new GoogleTranslate($lang, 'en');
        $bookTranslated = [$this->book->title, $this->book->description, $this->book->tags];
        if (preg_match('/[\x{0590}-\x{05FF}]/u', $this->book->description) === 0) {
            $bookTranslated = explode("#####", $tr->translate(join("\n#####\n", $bookTranslated)));
        }
        $this->book->title = trim($bookTranslated[0]);
        $this->book->description = trim($bookTranslated[1]);
        $this->book->tags = trim($bookTranslated[2]);
        if ($lang == "he") {
            $this->book->title = $n->handle($this->book->title);
            $this->book->description = $n->handle($this->book->description);
            $this->book->tags = $n->handle($this->book->tags);
        }

        $this->book->save();
    }
}
