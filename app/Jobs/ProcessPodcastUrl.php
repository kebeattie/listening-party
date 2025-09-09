<?php

namespace App\Jobs;

use App\Models\Podcast;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPodcastUrl implements ShouldQueue
{
    use Queueable;

    public $rssUrl;
    public $listeningParty;
    public $episode;

    /**
     * Create a new job instance.
     */
    public function __construct($rssUrl, $listeningParty, $episode)
    {
        $this->rssUrl = $rssUrl;
        $this->listeningParty = $listeningParty;
        $this->episode = $episode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {
                $xml = @simplexml_load_file($this->rssUrl);
                if ($xml === false) {
                    throw new \Exception("Failed to load RSS feed: {$this->rssUrl}");
                }

                $podcastTitle = (string) $xml->channel->title;
                $podcastArtwork = (string) $xml->channel->image->url ?? 'https://usercontent.one/wp/www.scholacampesina.org/wp-content/uploads/2023/02/podcast_lover.jpg?media=1708957596';

                // Use the rss_url as the unique identifier to find or create the podcast
                $podcast = Podcast::updateOrCreate(
                    ['rss_url' => $this->rssUrl],
                    [
                        'title' => $podcastTitle,
                        'artwork_url' => $podcastArtwork,
                    ]
                );

                // Check if the podcast record was created or retrieved successfully
                if (!$podcast) {
                    throw new \Exception("Podcast record could not be created or retrieved. The DB::transaction is likely failing and rolling back.");
                }

                $latestEpisode = $xml->channel->item[0];
                $episodeTitle = (string) $latestEpisode->title;
                $episodeMediaUrl = (string) $latestEpisode->enclosure['url'];

                $namespaces = $xml->getNamespaces(true);
                $itunesNamespace = $namespaces['itunes'] ?? null;
                $episodeLength = $itunesNamespace ? $latestEpisode->children($itunesNamespace)->duration : null;

//                $endTime = null;
                if ($episodeLength) {
                    $interval = CarbonInterval::createFromFormat('H:i:s', $episodeLength);
                    $endTime = $this->listeningParty->start_time->add($interval);
                }

                // Associate the episode with the podcast and update its attributes
                $this->episode->podcast()->associate($podcast);
                $this->episode->update([
                    'title' => $episodeTitle,
                    'media_url' => $episodeMediaUrl,
                ]);

                // Only update the listening party end time if it was calculated
                if ($endTime) {
                    $this->listeningParty->update([
                        'end_time' => $endTime,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            Log::error('ProcessPodcastUrl failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e; // Mark job as failed in the queue
        }
    }
}
