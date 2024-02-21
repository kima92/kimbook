<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Reading
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Reading newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reading newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reading query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reading whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reading whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reading whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reading whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reading whereUserId($value)
 * @mixin \Eloquent
 */
class Reading extends Model
{
    use HasFactory;



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
