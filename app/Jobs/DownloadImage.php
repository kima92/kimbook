<?php

namespace App\Jobs;

use App\Actions\GenerateBook;
use App\Models\Book;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI\Laravel\Facades\OpenAI;

class DownloadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Image $image)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $uuid = \Str::uuid()->toString();
        \Storage::put("public/images/{$uuid}.png", file_get_contents($this->image->image_url), ['visibility' => 'public']);

        $this->image->image_url = "/storage/images/{$uuid}.png";
        $this->image->save();
    }
}
