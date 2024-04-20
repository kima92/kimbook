<?php

namespace App\Http\Controllers;

use App\Actions\CheckCompleteBook;
use App\Actions\DownloadImageFromUrl;
use App\Models\Book;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReplicateController extends Controller
{
    public function instantId(Request $request, Book $book, Image $image)
    {
        Log::info("[ReplicateController][instantId] Got callback for book {$book->uuid} Image {$image->id}", $request->json()->all());

        if ($image->image_url) {
            Log::info("[ReplicateController][instantId] Already has image url");

            return response()->json();
        }

        (new DownloadImageFromUrl())->execute($image, $request->json("output.0"));

        Book::whereKey($book->id)->increment("additional_data->costs_usd", $request->json("metrics.predict_time") * "0.000725");

        (new CheckCompleteBook())->execute($book);

        return response()->json();
    }
}
