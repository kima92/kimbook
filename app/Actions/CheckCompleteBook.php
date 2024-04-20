<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 17/04/2024
 * Time: 18:49
 */

namespace App\Actions;

use App\Enums\BookStatuses;
use App\Events\BookCompleted;
use App\Models\Book;

class CheckCompleteBook
{
    public function execute(Book $book): void
    {
        if ($book->images()->whereNull("image_url")->first()) {
            // Not all images have path, means it callback implementation...?
            return;
        }

        $book->status = BookStatuses::Ready;
        $book->save();
        event(new BookCompleted($book));
    }
}
