<x-dashboard-tile :position="$position">
    <div
        wire:poll.{{ $refreshIntervalInSeconds }}s
        class="grid gap-2 justify-items-center h-full text-center"
    >
        @forelse ($forecasts as $forecast)
            @if ($loop->first)
                <div class="self-center font-bold text-5xl tracking-wide">
                    {{ round($forecast['temp']) }}&deg;
                </div>
                <div class="capitalize text-dimmed">
                    {{ $forecast['weather']['description'] }}
                </div>
            @else
                <div class="grid grid-cols-5 py-1">
                    <div class="col-span-2 flex items-center justify-start text-sm text-dimmed">
                        {{ $forecast['dayName'] }}
                    </div>
                    <div class="flex items-center justify-center text-dimmed leading-none">
                        @if ($forecast['weather']['id'] === 800)
                            @include('dashboard-weather-forecast-tile::icons.sun')
                        @elseif ($forecast['weather']['id'] === 801)
                            @include('dashboard-weather-forecast-tile::icons.sun-behind-small-cloud')
                        @elseif ($forecast['weather']['id'] === 802)
                            @include('dashboard-weather-forecast-tile::icons.sun-behind-large-cloud')
                        @elseif ($forecast['weather']['id'] === 803 || $forecast['weather']['id'] === 804)
                            @include('dashboard-weather-forecast-tile::icons.cloud')
                        @elseif (Illuminate\Support\Str::startsWith($forecast['weather']['id'], 3))
                            @include('dashboard-weather-forecast-tile::icons.cloud-with-rain')
                        @elseif (Illuminate\Support\Str::startsWith($forecast['weather']['id'], 5))
                            @include('dashboard-weather-forecast-tile::icons.sun-behind-rain-cloud')
                        @elseif (Illuminate\Support\Str::startsWith($forecast['weather']['id'], 2))
                            @include('dashboard-weather-forecast-tile::icons.cloud-with-lightning-and-rain')
                        @elseif (Illuminate\Support\Str::startsWith($forecast['weather']['id'], 6))
                            @include('dashboard-weather-forecast-tile::icons.snow-flake')
                        @elseif (Illuminate\Support\Str::startsWith($forecast['weather']['id'], 7))
                            @include('dashboard-weather-forecast-tile::icons.fog')
                        @else
                            {{-- error --}}
                        @endif
                    </div>
                    <div class="col-span-2 flex items-center divide-x justify-end text-xs text-dimmed">
                        <span class="pr-1">{{ round($forecast['temp']['morn']) }}&deg;</span>
                        <span class="px-1">{{ round($forecast['temp']['day']) }}&deg;</span>
                        <span class="pl-1">{{ round($forecast['temp']['eve']) }}&deg;</span>
                    </div>
                </div>
            @endif
        @empty
            No weather available.
        @endforelse
    </div>
</x-dashboard-tile>
