<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        return response()->json([
            'weather' => 'Mock: Sunny',
            'city' => $request->query('city'),
            'units' => $request->query('units')
        ]);
    }
}
