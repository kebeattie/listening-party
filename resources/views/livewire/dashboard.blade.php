<?php

use App\Models\Episode;
use App\Models\ListeningParty;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Jobs\ProcessPodcastUrl;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|date')]
    public $startTime = '';


    #[Validate('required|url')]
    public string $mediaUrl = '';

    public function createListeningParty()
    {
        $this->validate();

        $episode = Episode::create([
            'media_url'  => $this->mediaUrl,
            'title'      => $this->name,
            'start_time' => $this->startTime
        ]);

        $listeningParty = ListeningParty::create([
            'episode_id' => $episode->id,
            'name'       => $this->name,
            'start_time' => $this->startTime,
        ]);

        ProcessPodcastUrl::dispatch($this->mediaUrl, $listeningParty, $episode);

        return redirect()->route('parties.show', $listeningParty);
    }

    public function with()
    {
        return [
            'listeningParties' => ListeningParty::where('is_active', true)->orderBy('start_time', 'asc')->with('episode.podcast')->get(),
        ];
    }
}; ?>

<div class='min-h-screen bg-emerald-50 flex flex-col pt-8'>
    <div class='flex items-center justify-center p-4'>
        <div class='w-full max-w-lg'>
            <x-card shadow="lg" rounded="large">
                <h2 class="text-xl font-bold font-serif text-center">Let's listen together.</h2>
                <form wire:submit='createListeningParty()' class='space-y-6 mt-6'>
                    <x-input wire:model='name' placeholder='Listening Party Name'/>
                    <x-input wire:model='mediaUrl' placeholder="Podcast RSS Feed URL"
                             description='Entering RSS Feed will grab the latest episode'/>
                    <div class="relative">
                        <input
                            type="datetime-local"
                            wire:model="startTime"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                        />
                    </div>
                    <x-button type='submit' class='w-full' positive>Create Listening Party</x-button>
                </form>
            </x-card>
        </div>
    </div>
    <div class="my-20">
        <div class="max-w-lg mx-auto ">
            <h3 class="text-lg font-serif mb-8">Ongoing Listening Parties</h3>
            <div class="bg-white rounded-lg shadow-lg">
                @if($listeningParties->isEmpty())
                    <div class="flex items-center justify-center p-6 font-serif text-sm">No listening parties started yet...</div>
                @else
                @foreach($listeningParties as $listeningParty)
                    <div wire:key="{{ $listeningParty->id }}">
                        <a href="{{ route('parties.show', $listeningParty) }}" class="block">
                            <div class="flex items-center justify-between p-4 border-b border-gray-200 hover:bg-gray-50 duration-150 ease-in-out">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <x-avatar src="{{ $listeningParty->episode->podcast->artwork_url }}" size="xl" rounded="sm" alt="Podcast Artwork"/>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[0.9rem] font-semibold text-slate-900">{{$listeningParty->name}}</p>
                                        <div class="mt-0.8">
                                            <p class="text-sm truncate text-slate-600">{{$listeningParty->episode->title}}</p>
                                            <p class="text-slate-400 uppercase tracking-tighter text-[0.7rem]">{{$listeningParty->episode->podcast->title}}</p>
                                        </div>
                                        <div class="text-xs text-slate-600 mt-1" x-data="{
                                                startTime: {{$listeningParty->start_time->timestamp}},
                                            countDownText: '',
                                            isLive: false,
                                            updateCountdown() {
                                                const now = Math.floor(Date.now()/1000);
                                                const timeUntilStart = this.startTime - now;
                                                if (timeUntilStart <= 0) {
                                                    this.isLive = true;
                                                } else {
                                                    this.isLive = false;
                                                    const days = Math.floor(timeUntilStart / 86400);
                                                    const hours = Math.floor((timeUntilStart % 86400) / 3600);
                                                    const minutes = Math.floor((timeUntilStart % 3600) / 60);
                                                    const seconds = Math.floor(timeUntilStart % 60);

                                                    this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                                                    }
                                            }
                                        }" x-init="updateCountdown();
                                           setInterval(() => updateCountdown(), 1000);">
                                            <div x-show="isLive">
                                                <x-badge flat primary label="Live">
                                                    <x-slot name="prepend" class="relative flex items-center w-2 h-2">
                                                    <span
                                                        class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-rose-500 animate-ping"></span>

                                                        <span class="relative inline-flex w-2 h-2 rounded-full bg-rose-500"></span>
                                                    </x-slot>
                                                </x-badge>
                                            </div>
                                            <div x-show="!isLive">
                                              Starts in: <span x-text="countdownText"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <x-button positive flat xs class="w-20">Join</x-button>
                            </div>
                        </a>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
