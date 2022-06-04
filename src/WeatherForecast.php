<?php

namespace Solitweb\WeatherForecastTile;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class WeatherForecast
{
    public function getWeatherForecast(string $key, string $units, array $coordinates): array
    {
        list($lat, $lon) = $coordinates;

        $response = Http::get("https://api.openweathermap.org/data/3.0/onecall", [
            'lat' => $lat,
            'lon' => $lon,
            'exclude' => 'minutely,hourly',
            'units' => $units,
            'appid' => $key,
        ])->json();

        if (!array_key_exists('daily', $response) && !array_key_exists('current', $response)) {
            return [];
        }

        return collect($response['daily'])
            ->map(function (array $day) {
                return $this->format($day);
            })
            ->prepend($this->format($response['current']))
            ->toArray();
    }

    private function format($day)
    {
        $timestamp = Carbon::parse($day['dt']);

        return [
            'dayName' => $timestamp->dayName,
            'temp' => $day['temp'],
            'wind' => (array) [
                'speed' => $day['wind_speed'],
                'deg' => $day['wind_deg'],
                'gust' => $day['wind_gust'] ?? 0,
            ],
            'weather' => (array) $day['weather'][0],
        ];
    }

    private static function getV2Forecast($city, $key, $units)
    {
        $url = "https://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$key}&units={$units}";

        $response = Http::get($url)->json();

        if (!array_key_exists('list', $response)) {
            return [];
        }

        return collect($response['list'])
            ->map(function (array $forecast, int $key) {
                $timestamp = Carbon::parse($forecast['dt_txt']);

                if (($key === 0) || (!$timestamp->isToday() && $timestamp->isMidday())) {
                    return [
                        'dayName' => $timestamp->dayName,
                        'temp' => (int) $forecast['main']['temp'],
                        'wind' => (array) $forecast['wind'],
                        'weather' => (array) $forecast['weather'][0],
                    ];
                }
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
