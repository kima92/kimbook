<?php

namespace App\Models;

use App\Enums\BookStatuses;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Book
 *
 * @property int $id
 * @property BookStatuses $status
 * @property string $title
 * @property string $uuid
 * @property int $user_id
 * @property string|null $description
 * @property string|null $input
 * @property string|null $cover_image
 * @property \Illuminate\Support\Carbon $publication_date
 * @property string $tags
 * @property float|null $rating
 * @property array|null $additional_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chapter> $chapters
 * @property-read int|null $chapters_count
 * @property-read mixed $costs_usd
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reaction> $reactions
 * @property-read int|null $reactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reading> $readings
 * @property-read int|null $readings_count
 * @property-read mixed $status_message
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAdditionalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereInput($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePublicationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUuid($value)
 * @mixin \Eloquent
 */
class Book extends Model
{
    use HasFactory;

    protected static $unguarded = true;

    protected $casts = [
        "status"          => BookStatuses::class,
        "additional_data" => "array",
        "publication_date" => "date"
    ];

    protected $appends = ['status_message'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($book) {
            $book->uuid = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function readings()
    {
        return $this->hasMany(Reading::class);
    }

    protected function statusMessage(): Attribute
    {
        return Attribute::make(fn() => $this->status->translated());
    }
    protected function costsUsd(): Attribute
    {
        return Attribute::make(fn() => $this->additional_data['costs_usd'] ?? null);
    }

    public function toBookArray(): array
    {
        $pages = [
            ['isCover' => true, 'title' => $this->title, "content" => "מאת " . $this->user->name, 'image' => $this->chapters->first()->images->first()->image_url],
        ];

        $i = 1;
        foreach ($this->chapters as $chapter) {
            $pages[] = ['image' => $chapter->images->first()->image_url, 'title' => $chapter->title];
            $pages[] = ['content' => $chapter->content, 'pageNum' => $i++];
        }

        $pages[] = ['isCover' => true, 'image' => $this->chapters->first()->images->first()->image_url, 'title' => "הסוף :)"];

        return $pages;
    }
}
