<?php

namespace App\Listeners;

use App\Events\BookCompleted;
use Illuminate\Support\Facades\Mail;

class EmailNotificationsListener
{
    public function handleBookCompleted(BookCompleted $event)
    {
        \Log::info("[EmailNotificationsListener][handleBookCompleted] Notifying email about book completed", [
            "email" => $event->book->additional_data["request"]["email"] ?? $event->book->user->email,
        ]);

        Mail::to(
            $event->book->additional_data["request"]["email"] ?? $event->book->user
        )->send(new \App\Mail\BookCompleted($event->book));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(BookCompleted::class, [self::class, 'handleBookCompleted']);
    }
}
