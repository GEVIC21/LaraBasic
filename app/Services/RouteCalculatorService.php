<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Exceptions\GoogleMapsException;
use Illuminate\Support\Facades\Log;

class RouteCalculatorService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('googlemaps.key');

        if (empty($this->apiKey)) {
            throw new GoogleMapsException('Clé API Google Maps non configurée');
        }
    }

    public function calculateRoute($origin, $destination)
    {
        try {
            Log::debug('Tentative de calcul d\'itinéraire', [
                'origin' => $origin,
                'destination' => $destination
            ]);

            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->get("{$this->baseUrl}/directions/json", [
                'origin' => $origin,
                'destination' => $destination,
                'key' => $this->apiKey
            ]);

            Log::debug('Réponse Google Maps', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if (!$response->successful()) {
                throw new GoogleMapsException('Erreur de communication avec l\'API Google Maps');
            }

            $data = $response->json();

            if ($data['status'] !== 'OK') {
                throw new GoogleMapsException(
                    $data['error_message'] ?? 'Erreur lors du calcul de l\'itinéraire'
                );
            }

            return [
                'success' => true,
                'data' => [
                    'distance' => $data['routes'][0]['legs'][0]['distance'],
                    'duration' => $data['routes'][0]['legs'][0]['duration'],
                    'start_address' => $data['routes'][0]['legs'][0]['start_address'],
                    'end_address' => $data['routes'][0]['legs'][0]['end_address']
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Erreur Google Maps', [
                'message' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination
            ]);

            throw new GoogleMapsException(
                'Erreur lors du calcul de l\'itinéraire: ' . $e->getMessage()
            );
        }
    }

    public function reverseGeocode($latitude, $longitude)
    {
        $response = Http::get("{$this->baseUrl}/geocode/json", [
            'latlng' => "{$latitude},{$longitude}",
            'key' => $this->apiKey
        ]);

        if (!$response->successful()) {
            throw new GoogleMapsException('Erreur de géocodage inverse');
        }

        $data = $response->json();

        if ($data['status'] !== 'OK') {
            throw new GoogleMapsException($data['error_message'] ?? 'Erreur inconnue');
        }

        return $data['results'][0]['formatted_address'];
    }
}
