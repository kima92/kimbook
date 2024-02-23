<?php

namespace App\Jobs;

use App\Actions\GenerateBook;
use App\Enums\BookStatuses;
use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class StartGeneratingBook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(public Book $book)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(GenerateBook $gb): void
    {
        \Log::debug("[StartGeneratingBook][handle] Got book {$this->book->uuid}");
        $gb->handle($this->book);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        \Log::debug("[StartGeneratingBook][fail] Got book {$this->book->uuid} with message: {$exception->getMessage()}\n{$exception->getTraceAsString()}");
        $this->book->status = BookStatuses::FailedText;
        $this->book->save();
    }
}
