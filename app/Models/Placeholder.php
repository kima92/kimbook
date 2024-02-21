<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Placeholder
 *
 * @property int $id
 * @property int $book_id
 * @property string $name
 * @property string $default
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book $book
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder query()
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placeholder whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Placeholder extends Model
{
    use HasFactory;

    protected static $unguarded = true;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
