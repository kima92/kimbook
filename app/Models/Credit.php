<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Credit
 *
 * @property int $id
 * @property int $user_id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $amount
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $entity
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Credit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereUserId($value)
 * @mixin \Eloquent
 */
class Credit extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entity(): BelongsTo
    {
        return $this->morphTo("entity");
    }
}
