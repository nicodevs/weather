<?php

namespace Nicodevs\Weather;

use RuntimeException;

class OpenMeteo
{
    public function currentConditions(string $city): array
    {
        $place = $this->geocode($city);

        $data = $this->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $place['latitude'],
            'longitude' => $place['longitude'],
            'current' => 'temperature_2m,wind_speed_10m',
        ]);

        return [
            'city' => "{$place['name']}, {$place['country']}",
            'temperature' => $data['current']['temperature_2m'],
            'wind' => $data['current']['wind_speed_10m'],
        ];
    }

    private function geocode(string $city): array
    {
        $places = $this->get('https://geocoding-api.open-meteo.com/v1/search', ['name' => $city, 'count' => 1]);

        return $places['results'][0] ?? throw new RuntimeException("Could not find a city called {$city}");
    }

    private function get(string $url, array $query): array
    {
        return $url . '?' . http_build_query($query)
            |> file_get_contents(...)
            |> (fn ($json) => json_decode($json, true));
    }
}
