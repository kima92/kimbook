<?php

namespace App\Listeners;

use App\Events\BookCompleted;
use App\Events\PaymentCompleted;
use App\Models\Credit;
use Illuminate\Auth\Events\Registered;
class CreditsListener
{
    public function handleUserRegistered(Registered $event)
    {
        $credit = new Credit();
        $credit->user()->associate($event->user);
        $credit->entity()->associate($event->user);
        $credit->amount = config("credits.amounts.registered");
        $credit->message = "דמי הרשמה";
        $credit->save();
    }

    public function handleBookCompleted(BookCompleted $event)
    {
        $credit = new Credit();
        $credit->user()->associate($event->book->user);
        $credit->entity()->associate($event->book);
        $credit->amount = -config("credits.amounts.book");
        $credit->message = "יצירת ספר";
        $credit->save();
    }

    public function handlePaymentCompleted(PaymentCompleted $event)
    {
        $credit = new Credit();
        $credit->user()->associate($event->payment->user);
        $credit->entity()->associate($event->payment);
        $credit->amount = $event->payment->credits;
        $credit->message = "עבור תשלום";
        $credit->save();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(Registered::class, [self::class, 'handleUserRegistered']);
        $events->listen(BookCompleted::class, [self::class, 'handleBookCompleted']);
        $events->listen(PaymentCompleted::class, [self::class, 'handlePaymentCompleted']);
    }
}
