<?php

namespace App\Listeners;

use App\Events\BookCompleted;
use Illuminate\Support\Facades\Mail;

class EmailNotificationsListener
{
    public function handleBookCompleted(BookCompleted $event)
    {
        Mail::to($event->book->user)->send(new \App\Mail\BookCompleted($event->book));
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
