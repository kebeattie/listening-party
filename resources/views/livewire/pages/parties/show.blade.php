<?php

use Livewire\Volt\Component;
use App\Models\ListeningParty;

new class extends Component {

    public ListeningParty $listeningParty;

    public function mount(ListeningParty $listeningParty)
    {
        $this->listeningParty = $listeningParty->load('episode.podcast');
    }
}; ?>

<div x-data="{
        audio: null,
        isLoading: true,
        isLive: false,
        isPlaying: false,
        isReady: false,
        currentTime: 0,
        countdownText: '',
        startTimestamp: {{ $listeningParty->start_time->timestamp }},

        initializeAudioPlayer() {
            this.audio = this.$refs.audioPlayer;
            this.audio.addEventListener('loadedmetadata', () => {
                this.isLoading = false;
                this.isReady = true;
            });
            this.audio.addEventListener('timeupdate', () => {
                this.currentTime = this.audio.currentTime;
            });
            this.audio.addEventListener('play', () => {
                this.isPlaying = true;
            });
            this.audio.addEventListener('pause', () => {
                this.isPlaying = false;
            });
            // Start countdown and live check
            this.checkAndUpdate();
            setInterval(() => this.checkAndUpdate(), 1000);
        },

        checkAndUpdate() {
            const now = Math.floor(Date.now() / 1000);
            const timeUntilStart = this.startTimestamp - now;

            if (timeUntilStart  <= 0) {
                if(!this.isPlaying) {
                    this.isLive = true;
                    if (this.isReady) {
                        this.audio.play().catch((error) => {
                            console.error('Playback failed', error);
                        });
                    }
                }
            } else {
                const days = Math.floor(timeUntilStart / 86400);
                const hours = Math.floor((timeUntilStart % 86400) / 3600);
                const minutes = Math.floor((timeUntilStart % 3600) / 60);
                const seconds = Math.floor(timeUntilStart % 60);
                this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            }

        },

        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return `${minutes}:${remainingSeconds.toString().padStart(2,'0')}`;
        }
        }" x-init="initializeAudioPlayer()">
    @if($listeningParty->end_time == null)
        <div class="flex items-center justify-center p-6 font-serif text-sm" wire:poll.5s>
            Creating your <span class="font-bold">{{ $listeningParty->name }}</span>
            listening party...
        </div>
    @else
    <div>
        <audio x-ref="audioPlayer" :src="'{{ $listeningParty->episode->media_url }}'" preload="auto"></audio>
        <div>{{ $listeningParty->episode->podcast->title }}</div>
        <div>{{ $listeningParty->episode->title }}</div>
        <div>Current Time: <span x-text="formatTime(currentTime)"></span></div>
        <div>Start Time: <span>{{ $listeningParty->start_time }}</span></div>
        <div x-show="isLoading">Loading...</div>
    </div>
    @endif
</div>
