<?php

namespace App\Listeners;

use App\Events\BookCompleted;
use App\Events\BookCreated;
use App\Events\BookFailed;
use App\Events\BookReviewUpdated;
use App\Events\BookWritten;
use App\Events\PaymentCompleted;
use App\Models\Credit;
use App\Utils\Telegram;
use Illuminate\Auth\Events\Registered;

class TelegramNotificationsListener
{
    public function handleBookCreated(BookCreated $event)
    {
        (new Telegram())->send("\xF0\x9F\x93\x98New Book {$event->book->id} {$event->book->uuid} Created!\n<b>Input: </b>{$event->book->input}");
    }
    public function handleBookWritten(BookWritten $event)
    {
        (new Telegram())->send("\xF0\x9F\x93\x96Book Written!\n<b><a href=\"" . url("books/" . $event->book->uuid) . "\">{$event->book->id}. {$event->book->title}</a></b>");
    }

    public function handleBookCompleted(BookCompleted $event)
    {
        $time = $event->book->created_at->diffInSeconds(now());
        (new Telegram())->send("\xF0\x9F\x93\x97Book {$event->book->id} Completed!\nTotal Cost: {$event->book->additional_data["costs_usd"]}$\nTook <b>{$time} seconds</b>");
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

    public function handleBookReviewUpdated(BookReviewUpdated $event)
    {
        (new Telegram())->send("\xE2\x9C\x89\xE2\x9C\x89Book <b><a href=\"" . url("books/" . $event->book->uuid) . "\">{$event->book->id}. {$event->book->title}</a></b> Review updated: {$event->book->additional_data["userReview"]}");
    }

    public function handlePaymentCompleted(PaymentCompleted $event)
    {
        (new Telegram())->send("\xF0\x9F\x92\xB8\xF0\x9F\x92\xB8\xF0\x9F\x92\xB8New Payment\xF0\x9F\x92\xB8\xF0\x9F\x92\xB8\xF0\x9F\x92\xB8\n".
        "<b>User:</b> {$event->payment->user->id}. {$event->payment->user->name}\n".
        "<b>Price:</b> {$event->payment->price}₪ <b>Credits: </b> {$event->payment->credits}");
    }

    public function handleUserRegistered(Registered $event)
    {
        (new Telegram())->send("\xE2\x9C\x8C\xE2\x9C\x8C\xE2\x9C\x8CNew User\xE2\x9C\x8C\xE2\x9C\x8C\xE2\x9C\x8C\xE2\x9C\x8C\n".
                               "<b>Name:</b> {$event->user->name}\n".
                               "<b>Email:</b> {$event->user->email}");
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
        $events->listen(BookReviewUpdated::class, [self::class, 'handleBookReviewUpdated']);

        $events->listen(PaymentCompleted::class, [self::class, 'handlePaymentCompleted']);

        $events->listen(Registered::class, [self::class, 'handleUserRegistered']);

    }
}
