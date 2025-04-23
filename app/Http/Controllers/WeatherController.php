<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $units = $request->query('units', 'metric'); // default to metric
        $apiKey = env('OPENWEATHERMAP_API_KEY');

        \Log::info("Using API key: $apiKey"); 

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Missing coordinates'], 400);
        }

        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $apiKey,
            'units' => $units
        ]);

        $data = $response->json();

        return response()->json([
            'weather' => ucfirst($data['weather'][0]['description']),
            'city' => $data['name'],
            'units' => $request->query('units', 'metric')
        ]);

    }
}
