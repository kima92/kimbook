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
        if ($book->status == BookStatuses::Ready) {
            return;
        }

        if ($book->images()->whereNull("image_url")->first()) {
            // Not all images have path, means it callback implementation...?
            return;
        }

        if (Book::query()->whereKey($book->id)->toBase()->update(["status" => BookStatuses::Ready])) {
            \Log::info("[CheckCompleteBook][execute] ");
            event(new BookCompleted($book));
        }
    }
}
