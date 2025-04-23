<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $units = $request->query('units', 'metric'); // default to metric
        $city = $request->query('city');
        $apiKey = env('OPENWEATHERMAP_API_KEY');

        \Log::info("Using API key: $apiKey");

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Missing coordinates'], 400);
        }

        // Current weather
        $currentResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $apiKey,
            'units' => $units
        ]);
        $currentData = $currentResponse->json();

        // Forecast
        $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $apiKey,
            'units' => $units
        ]);
        $forecastData = $forecastResponse->json();

        $forecastDays = [];
        $seenDays = [];

        foreach ($forecastData['list'] as $entry) {
            $date = Carbon::parse($entry['dt_txt']);
            $dayName = $date->format('l');

            if (!in_array($dayName, $seenDays) && $date->hour === 12) {
                $forecastDays[] = [
                    'day' => $dayName,
                    'temp' => round($entry['main']['temp']),
                    'icon' => $entry['weather'][0]['icon'],
                ];
                $seenDays[] = $dayName;
            }

            if (count($forecastDays) >= 3) break;
        }

        return response()->json([
            'weather' => ucfirst($currentData['weather'][0]['description']),
            'icon' => $currentData['weather'][0]['icon'],
            'city' => $city ?? $currentData['name'],
            'units' => $units,
            'temperature' => round($currentData['main']['temp']),
            'date' => now()->toDateString(),
            'windSpeed' => $currentData['wind']['speed'],
            'humidity' => $currentData['main']['humidity'],
            'forecast' => $forecastDays,
        ]);
    }
}
