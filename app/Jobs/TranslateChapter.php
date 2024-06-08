<?php

namespace App\Jobs;

use App\Actions\GenerateBook;
use App\Actions\Niqqud;
use App\Models\Book;
use App\Models\Chapter;
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

class TranslateChapter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Chapter $chapter)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug("[TranslateChapter][handle] Translating chapter {$this->chapter->id}");

        $lang = $this->book->additional_data["request"]["language"] ?? "he";

        $n = new Niqqud();
        $tr = new GoogleTranslate($lang, 'en');
        $chapterTranslated = [$this->chapter->title, $this->chapter->content];

        if (preg_match('/[\x{0590}-\x{05FF}]/u', $this->chapter->content) === 0) {
            $chapterTranslated = explode("#####", $tr->translate(join("\n#####\n", $chapterTranslated)));
            Log::debug("[TranslateChapter][handle] Translation response [{$chapterTranslated[0]}] {$chapterTranslated[1]}");
        }
        $this->chapter->title = trim($chapterTranslated[0]);
        $this->chapter->content = trim($chapterTranslated[1]);
        if ($lang == "he") {
            $this->chapter->title = $n->handle($this->chapter->title);
            $this->chapter->content = $n->handle($this->chapter->content);
        }

        $this->chapter->save();
    }
}
