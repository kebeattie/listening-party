<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $podcast_id
 * @property string|null $title
 * @property string $media_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ListeningParty|null $listeningParties
 * @property-read \App\Models\Podcast|null $podcast
 * @method static \Database\Factories\EpisodeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereMediaUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode wherePodcastId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Episode extends Model
{
    /** @use HasFactory<\Database\Factories\EpisodeFactory> */
    use HasFactory;

    protected $guarded = ['id'];
    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }

    public function listeningParties(): BelongsTo
    {
        return $this->belongsTo(ListeningParty::class);
    }
}
