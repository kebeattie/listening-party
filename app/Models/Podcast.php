<?php

namespace App\Models;

use Database\Factories\PodcastFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $rss_url
 * @property string|null $hosts
 * @property string|null $artwork_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Episode> $episodes
 * @property-read int|null $episodes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ListeningParty> $listeningParties
 * @property-read int|null $listening_parties_count
 * @method static \Database\Factories\PodcastFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereArtworkUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereHosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereRssUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Podcast extends Model
{
    /** @use HasFactory<PodcastFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function listeningParties(): HasMany
    {
        return $this->hasMany(ListeningParty::class);
    }
}


