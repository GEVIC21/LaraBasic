<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RouteCalculatorService;
use App\Http\Requests\RouteCalculationRequest;

class MapController extends Controller
{
    protected $routeCalculatorService;

    public function __construct(RouteCalculatorService $routeCalculatorService)
    {
        $this->routeCalculatorService = $routeCalculatorService;
    }

    public function index()
    {
        return view('maps.index');
    }

    public function calculateRoute(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string'
        ]);

        try {
            $result = $this->routeCalculatorService->calculateRoute(
                $request->origin,
                $request->destination
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }


    public function getCurrentLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        try {
            $address = $this->routeCalculatorService->reverseGeocode(
                $request->latitude,
                $request->longitude
            );

            return response()->json([
                'success' => true,
                'address' => $address
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
