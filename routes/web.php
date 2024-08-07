<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CharactersController;
use App\Http\Controllers\CreditsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('/books', [BookController::class, "store"]);
Route::get('/books/{uuid}', [BookController::class, "show"]);
Route::put('/books/{uuid}', [BookController::class, "update"]);
Route::get('/books/{uuid}/slim', [BookController::class, "showSlim"]);
Route::get('/books/{uuid}/print', [BookController::class, "print"]);
Route::get('/books/{uuid}/next', [BookController::class, "next"])->middleware(['auth', 'verified']);

Route::get('/oauth/redirect', fn() => Socialite::driver('google')->redirect());
Route::get('/oauth', [RegisteredUserController::class, "oauth"]);

//Route::middleware(['auth', 'verified'])->group(function () {
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/books', [BookController::class, "index"])->name('books');
    Route::get('/characters', [CharactersController::class, "index"])->name('characters');
    Route::post('/characters', [CharactersController::class, "store"])->name('characters-store');
    Route::get('/credits', [CreditsController::class, "index"])->name('credits');
    Route::get('/payments', [PaymentsController::class, "store"]);
    Route::get('/payments/redirect', [PaymentsController::class, "redirect"]);
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/login-anon', function () {
    \Illuminate\Support\Facades\Auth::login(\App\Models\User::query()->firstWhere("email", "anonimous@sipuron.co.il"));

    return redirect('/dashboard');
});


require __DIR__.'/auth.php';
