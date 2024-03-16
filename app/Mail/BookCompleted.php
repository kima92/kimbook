<?php

namespace App\Mail;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookCompleted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Book $book)
    {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("הספר החדש שלכם מחכה!")
            ->with([
                "book" => $this->book,
            ])
            ->view('mails.book-completed');
    }
}
