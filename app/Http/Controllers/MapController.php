<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RouteCalculatorService;
use App\Http\Requests\RouteCalculationRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapController extends Controller
{
    protected $routeCalculatorService;
    protected $googleMapsKey;

    public function __construct(RouteCalculatorService $routeCalculatorService)
    {
        $this->routeCalculatorService = $routeCalculatorService;
        $this->googleMapsKey = config('googlemaps.key');
    }

    public function index()
    {
        return view('maps.index');
    }

    public function calculateRoute(Request $request)
    {
        try {
            Log::info('Début du calcul d\'itinéraire', $request->all());

            $validated = $request->validate([
                'origin' => 'required|string|min:3',
                'destination' => 'required|string|min:3'
            ]);

            if (empty($this->googleMapsKey)) {
                throw new \Exception('Clé API Google Maps non configurée');
            }

            // Ajout des options pour gérer le SSL et améliorer la résilience
            $response = Http::withOptions([
                'verify' => env('APP_ENV') === 'local' ? false : true,
                'connect_timeout' => 10,
                'timeout' => 15,
                'http_errors' => true
            ])->get('https://maps.googleapis.com/maps/api/directions/json', [
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
                'key' => $this->googleMapsKey,
                'language' => 'fr',
                'region' => 'tg',
                'alternatives' => true,
                'mode' => 'driving',
                'units' => 'metric'
            ]);

            if (!$response->successful()) {
                Log::error('Erreur API Google Maps', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $response->effectiveUri()
                ]);
                throw new \Exception('Erreur lors de la communication avec Google Maps: ' . $response->status());
            }

            $data = $response->json();

            // Vérification du statut de l'API Google Maps
            if ($data['status'] !== 'OK') {
                Log::warning('Erreur API Google Maps', [
                    'status' => $data['status'],
                    'message' => $data['error_message'] ?? 'Aucun message d\'erreur',
                    'request' => [
                        'origin' => $validated['origin'],
                        'destination' => $validated['destination']
                    ]
                ]);

                $errorMessage = match ($data['status']) {
                    'ZERO_RESULTS' => 'Aucun itinéraire trouvé entre ces deux points',
                    'NOT_FOUND' => 'Impossible de localiser un des points spécifiés',
                    'OVER_QUERY_LIMIT' => 'Quota d\'utilisation dépassé',
                    'REQUEST_DENIED' => 'Requête refusée par le service',
                    'INVALID_REQUEST' => 'Requête invalide',
                    default => 'Erreur lors du calcul de l\'itinéraire'
                };

                throw new \Exception($errorMessage);
            }

            if (empty($data['routes'])) {
                throw new \Exception('Aucun itinéraire disponible');
            }

            // Traitement de l'itinéraire principal
            $mainRoute = $data['routes'][0]['legs'][0];
            $alternatives = [];

            // Traitement des itinéraires alternatifs
            foreach (array_slice($data['routes'], 1) as $routeData) {
                $leg = $routeData['legs'][0];
                $alternatives[] = [
                    'distance' => $leg['distance'],
                    'duration' => $leg['duration'],
                    'start_address' => $leg['start_address'],
                    'end_address' => $leg['end_address']
                ];
            }

            // Construction de la réponse
            $response = [
                'success' => true,
                'data' => [
                    'distance' => $mainRoute['distance'],
                    'duration' => $mainRoute['duration'],
                    'start_address' => $mainRoute['start_address'],
                    'end_address' => $mainRoute['end_address'],
                    'steps' => array_map(function ($step) {
                        return [
                            'distance' => $step['distance'],
                            'duration' => $step['duration'],
                            'instructions' => strip_tags($step['html_instructions']),
                            'maneuver' => $step['maneuver'] ?? null,
                        ];
                    }, $mainRoute['steps']),
                    'alternatives' => $alternatives,
                    'bounds' => $data['routes'][0]['bounds'],
                    'overview_polyline' => $data['routes'][0]['overview_polyline'] ?? null
                ]
            ];

            Log::info('Itinéraire calculé avec succès', [
                'origin' => $validated['origin'],
                'destination' => $validated['destination']
            ]);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Erreur lors du calcul d\'itinéraire', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul de l\'itinéraire: ' . $e->getMessage()
            ], 422);
        }
    }


    public function getCurrentLocation(Request $request)
    {
        try {
            Log::info('Début de la géolocalisation', $request->all());

            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180'
            ]);

            if (empty($this->googleMapsKey)) {
                throw new \Exception('Clé API Google Maps non configurée');
            }

            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$validated['latitude']},{$validated['longitude']}",
                'key' => $this->googleMapsKey,
                'language' => 'fr',
                'region' => 'tg' // Spécifique au Togo
            ]);

            if (!$response->successful()) {
                Log::error('Erreur API Google Maps Geocoding', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Erreur lors de la communication avec Google Maps');
            }

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                // Filtrer pour obtenir l'adresse la plus précise
                $result = collect($data['results'])
                    ->sortByDesc(function ($result) {
                        return count($result['address_components']);
                    })
                    ->first();

                return response()->json([
                    'success' => true,
                    'address' => $result['formatted_address'],
                    'place_id' => $result['place_id'],
                    'location' => $result['geometry']['location']
                ]);
            }

            Log::warning('Adresse non trouvée', [
                'status' => $data['status'],
                'error_message' => $data['error_message'] ?? 'Aucun message d\'erreur'
            ]);

            throw new \Exception($data['error_message'] ?? 'Adresse non trouvée');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la géolocalisation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la géolocalisation: ' . $e->getMessage()
            ], 422);
        }
    }
}
