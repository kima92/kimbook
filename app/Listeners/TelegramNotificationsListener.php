<?php

namespace App\Listeners;

    use App\Events\BookCompleted;
    use App\Events\BookCreated;
    use App\Events\BookFailed;
    use App\Events\BookWritten;
    use App\Utils\Telegram;

    class TelegramNotificationsListener
{
    public function handleBookCreated(BookCreated $event)
    {
        (new Telegram())->send("\xF0\x9F\x93\x98New Book {$event->book->id} {$event->book->uuid} Created!\n<b>Input: </b>{$event->book->input}</b>");
    }
    public function handleBookWritten(BookCreated $event)
    {
        (new Telegram())->send("\xF0\x9F\x93\ Book Written!\n<b><a href=\"" . url("books/" . $event->book->uuid) . "\">{$event->book->id}. {$event->book->title}</a></b>");
    }

    public function handleBookCompleted(BookCompleted $event)
    {
        $time = $event->book->created_at->diffInSeconds(now());
        (new Telegram())->send("\xF0\x9F\x93\Book {$event->book->id} Completed!\nTotal Cost: {$event->book->additional_data["costs_usd"]}$\nTook <b>{$time} seconds</b>");
    }

    public function handleBookFailed(BookFailed $event)
    {
        $time = $event->book->created_at->diffInSeconds(now());
        $errorMessage = $event->book->additional_data["error"]["error_message"] ?? 'No message';
        if ($words = $event->book->additional_data["error"]["error_keywords"] ?? null) {
            $errorMessage .= " - " .implode(", ", $words);
        }
        (new Telegram())->send("\xE2\x9B\x94\xE2\x9B\x94\xE2\x9B\x94Book {$event->book->id} Failed !\nReason: {$errorMessage}\nTook <b>{$time} seconds</b>");
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(BookCreated::class, [self::class, 'handleBookCreated']);
        $events->listen(BookWritten::class, [self::class, 'handleBookWritten']);
        $events->listen(BookCompleted::class, [self::class, 'handleBookCompleted']);
        $events->listen(BookFailed::class, [self::class, 'handleBookFailed']);
    }
}
