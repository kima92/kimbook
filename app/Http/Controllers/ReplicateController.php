<?php

namespace App\Http\Controllers;

use App\Actions\CheckCompleteBook;
use App\Actions\DownloadImageFromUrl;
use App\Actions\GenerateBook;
use App\AI\Chat\ReplicateLlama3Conversation;
use App\Enums\BookStatuses;
use App\Models\Book;
use App\Models\Image;
use BenBjurstrom\Replicate\Replicate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    public function photomakerStyle(Request $request, Book $book, Image $image)
    {
        Log::info("[ReplicateController][photomakerStyle] Got callback for book {$book->uuid} Image {$image->id}", $request->json()->all());

        if ($image->image_url) {
            Log::info("[ReplicateController][photomakerStyle] Already has image url");

            return response()->json();
        }

        (new DownloadImageFromUrl())->execute($image, $request->json("output.0"));

        Book::whereKey($book->id)->increment("additional_data->costs_usd", $request->json("metrics.predict_time") * "0.000725");

        (new CheckCompleteBook())->execute($book);

        return response()->json();
    }

    public function sdxlLightning4Step(Request $request, Book $book, Image $image)
    {
        Log::info("[ReplicateController][sdxlLightning4Step] Got callback for book {$book->uuid} Image {$image->id}", $request->json()->all());

        if ($image->image_url) {
            Log::info("[ReplicateController][sdxlLightning4Step] Already has image url");

            return response()->json();
        }

        (new DownloadImageFromUrl())->execute($image, $request->json("output.0"));

        Book::whereKey($book->id)->increment("additional_data->costs_usd", $request->json("metrics.predict_time") * "0.00115");

        (new CheckCompleteBook())->execute($book);

        return response()->json();
    }

    public function llama3(Request $request, $id)
    {
        Log::info("[ReplicateController][llama3] Got callback for id {$id}", $request->json()->all());

        if (Str::startsWith($id, "generate_book:")) {
            $book = Book::whereKey(Str::after($id, ":"))->firstOrFail();

            // Check already got answer
            if (!in_array($book->status, [BookStatuses::Initial, BookStatuses::GeneratingText, BookStatuses::FailedText])) {
                Log::info("[ReplicateController][llama3] Already has content");

                return response()->json();
            }

            $outputArray = $request->json("output");
            $countHasSpace = count(array_filter($outputArray, fn ($el) => Str::startsWith($el, " ") || Str::endsWith($el, " ")));
            $separator = count($outputArray) / $countHasSpace > 0.25;
            $output = join($separator, $outputArray);

            Log::debug("[ReplicateController][llama3] Output: {$output}");

            $chatConv = (new ReplicateLlama3Conversation(
                app(Replicate::class),
                [
                    ['role' => 'user', 'content' => $request->json("input.prompt")],
                    ['role' => 'assistant', 'content' => $output],
                ],
                [['input_tokens' => $request->json("metrics.input_token_count"), 'output_tokens' => $request->json("metrics.output_token_count")]]
            ))->setId($id);

            // TODO: Retry on bad response?

            (new GenerateBook($chatConv))->completeBook($book, $output);
        }

        return response()->json();
    }
}
