<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $episode_id
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Episode $episode
 * @method static \Database\Factories\ListeningPartyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereEpisodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningParty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ListeningParty extends Model
{
    /** @use HasFactory<\Database\Factories\ListeningPartyFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
