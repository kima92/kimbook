<?php

namespace App\Http\Controllers;

use App\Enums\BookStatuses;
use App\Events\BookCreated;
use App\Events\BookReviewUpdated;
use App\Jobs\StartGeneratingBook;
use App\Models\Book;
use App\Models\Reading;
use App\Models\User;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function index(Request $request)
    {
        return view('books.index');
    }

    public function show(Request $request, string $uuid)
    {
        $book = Book::query()
            ->with('chapters.images', 'reactions')
            ->where('uuid', $uuid)
            ->firstOrFail();

        if ($request->expectsJson()) {
            return $book->toArray();
        }

        $canView = true;
        /** @var ?User $user */
        if ($user = $request->user()) {
            // TODO: ViewService? return the view according to subscription?
            if ($canView = !$user->exceededViews($book)) {
                $reading = new Reading();
                $reading->user()->associate($user);
                $reading->book()->associate($book);
                $reading->save();
            }
        }

        return view('books.show', ['book' => $book, "canView" => $canView]);
    }

    public function update(Request $request, string $uuid)
    {
        $book = Book::query()
            ->with('chapters.images', 'reactions')
            ->where('uuid', $uuid)
            ->firstOrFail();

        $book->forceFill(["additional_data->userReview" => $request->json("review")]);
        $book->save();

        event(new BookReviewUpdated($book));

        return \response()->json();
    }

    public function next(Request $request, string $uuid)
    {
        // TODO: Real logic
        $id = rand(0, Book::max("id"));
        $nextUuid = Book::query()
            ->where("id", "<", $id)
            ->where("status", BookStatuses::Ready)
//            ->where('user_id', $request->user()->id)
            ->value('uuid') ?? Book::query()
            ->where("status", BookStatuses::Ready)
            ->where("id", ">", $id)
//            ->where('user_id', $request->user()->id)
            ->value('uuid');


        return redirect("/books/{$nextUuid}");
    }

    public function showSlim(Request $request, string $uuid)
    {
        $book = Book::query()
            ->with('chapters.images', 'reactions')
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('books.showSlim', compact('book'));
    }

    public function store(Request $request)
    {
        Log::debug("[BookController][store] Got new request");

        $balance = \Auth::user()->credits()->sum("amount");
        if ($balance - config('credits.amounts.book') < 0) {
            return response()->json([
                "error_message" => "אין מספיק קרדיטים ליצירת סיפור חדש ({$balance})"
            ], Response::HTTP_PAYMENT_REQUIRED);
        }

        $request->mergeIfMissing(["isAdultReader" => false]);

        // Minimum prompt word count

        // Todo: Validate
        $book = new Book();
        $book->status = BookStatuses::Initial;
        $book->title = "";
        $book->description = null;
        $book->input = $request->input("plot");
        $book->uuid = \Str::uuid()->toString();
        $book->publication_date = now();
        $book->additional_data = ["request" => $request->only(["age", "moral", "isAdultReader", "language", "pictures", "art-style"])];
        $book->tags = "";
        $book->user()->associate($request->user());

        $book->save();

        event(new BookCreated($book));
        dispatch(new StartGeneratingBook($book));

        return response()->json(["uuid" => $book->uuid]);
    }
}
