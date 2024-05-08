<?php

namespace App\Jobs;

use App\Actions\DownloadImageFromUrl;
use App\AI\Art\DalE3;
use App\AI\Art\GenerateImageResult;
use App\AI\Art\ReplicateInstantId;
use App\AI\Art\ReplicateSdxlLightning4Step;
use App\AI\GenerateAIStatuses;
use App\Models\Image;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI\Contracts\ClientContract;

class GenerateImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public Image $image)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::debug("[GenerateImage][handle] Got image {$this->image->id} with prompt '{$this->image->prompt}'");

        /** @var GenerateImageResult $result */
        if ($this->image->book->additional_data["request"]["character"] ?? null) {
            $result = retry(1, fn() => app(ReplicateInstantId::class)->create($this->image));
        } else {
            $result = retry(1, fn() => app(ReplicateSdxlLightning4Step::class)->create($this->image));
//            $result = retry(1, fn() => (new DalE3(app(ClientContract::class)))->create($this->image));
        }

        if ($result->status == GenerateAIStatuses::Completed) {
            (new DownloadImageFromUrl())->execute($this->image, $result->images[0]);
        }
    }
}
