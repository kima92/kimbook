<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Reaction
 *
 * @property int $id
 * @property int $book_id
 * @property int $user_id
 * @property string $reaction_type
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book $book
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereReactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereUserId($value)
 * @mixin \Eloquent
 */
class Reaction extends Model
{
    use HasFactory;

    protected static $unguarded = true;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
