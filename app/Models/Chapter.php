<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Chapter
 *
 * @property int $id
 * @property int $book_id
 * @property int $number
 * @property string $title
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book $book
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chapter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Chapter extends Model
{
    use HasFactory;

    protected static $unguarded = true;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
