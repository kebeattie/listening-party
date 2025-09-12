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
            this.isLive = true;
            this.audio = this.$refs.audioPlayer;
            this.audio.addEventListener('loadedmetadata', () => {
                this.isLoading = false;
            });
            this.audio.addEventListener('timeupdate', () => {
                this.currentTime = this.audio.currentTime;
            });
            this.audio.addEventListener('play', () => {
                this.isPlaying = true;
                this.isReady = true;
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
            // If end_time is available, use it; otherwise, only check start_time
            const endTimestamp = typeof window.endTimeTimestamp !== 'undefined' ? window.endTimeTimestamp : null;

            if (timeUntilStart <= 0 && (!endTimestamp || now < endTimestamp)) {
                this.isLive = true;
                this.playAudio();
            } else {
                this.isLive = false;
                const days = Math.floor(timeUntilStart / 86400);
                const hours = Math.floor((timeUntilStart % 86400) / 3600);
                const minutes = Math.floor((timeUntilStart % 3600) / 60);
                const seconds = Math.floor(timeUntilStart % 60);
                this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            }
        },

        playAudio() {
            const now = Math.floor(Date.now() / 1000);
            const elapsedTime = Math.max(0, now - this.startTimestamp);
            this.audio.currentTime = elapsedTime;
            this.audio.play().catch((error) => {
                console.error('Playback failed', error);
                this.isPlaying = false;
                this.isReady = false;
            });

        },

        joinAndBeReady() {
            this.isReady = true;
            if (this.isLive){
                this.playAudio();
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
        <audio x-ref="audioPlayer" :src="'{{ $listeningParty->episode->media_url }}'" preload="auto"></audio>
        <div class="flex items-center justify-center min-h-screen bg-emerald-50">
            <div class="w-full max-w-2xl shadow-lg rounded-lg bg-white p-8">  
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <x-avatar src="{{ $listeningParty->episode->podcast->artwork_url }}" size="xl" rounded="sm"
                            alt="Podcast Artwork" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[0.9rem] font-semibold text-slate-900">{{$listeningParty->name}}</p>
                        <div class="mt-0.8">
                            <p class="text-sm truncate text-slate-600">{{$listeningParty->episode->title}}</p>
                            <p class="text-slate-400 uppercase tracking-tighter text-[0.7rem]">
                                {{$listeningParty->episode->podcast->title}}</p>
                        </div>
                    </div>
                </div>
                <template x-if="!isLive">
                    <div>
                        <p class="text-slate-700 text-l font-bold mt-4">
                            Starts in: <span x-text="countdownText"></span>
                        </p>
                        <x-button x-show="!isReady" class="w-full mt-8" @click="joinAndBeReady">Join And Be Ready </x-button>
                        <h2 x-show="isReady" class="text-lg text-green-600 font-bold text-center font-serif tracking-tight mt-8">The audio will play when the countdown finishes</h2>
                    </div>
                </template>
                <template x-if="isLive">
                    <div class="mt-4">
                        <div>Current Time: <span x-text="formatTime(currentTime)"></span></div>
                        <div>Start Time: <span>{{ $listeningParty->start_time }}</span></div>
                        <div x-show="isLoading">Loading...</div>
                        <x-button x-show="!isReady" @click="joinAndBeReady" class="w-full mt-4">Join And Be Ready</x-button>
                    </div>
                </template>
            </div>
        </div>
    @endif
</div>