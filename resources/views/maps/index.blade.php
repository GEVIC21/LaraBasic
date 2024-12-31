@extends('layouts.master')
@section('content')
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-4">Calculateur d'Itinéraire</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Planification d'Itinéraire</h5>
                    </div>
                    <div class="card-body">
                        <!-- Formulaire d'itinéraire -->
                        <div class="form-group mb-4">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        <input id="origin" type="text" class="form-control"
                                            placeholder="Point de départ">
                                        <button class="btn btn-outline-secondary" type="button" id="useCurrentLocation">
                                            <i class="fas fa-location-arrow"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-flag-checkered"></i>
                                        </span>
                                        <input id="destination" type="text" class="form-control"
                                            placeholder="Destination">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button id="calculateRoute" class="btn btn-primary w-100">
                                        <i class="fas fa-route me-2"></i>Calculer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Informations sur l'itinéraire -->

                        <div id="routeInfo" class="alert alert-info d-none mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <i class="fas fa-road me-2"></i>Distance:
                                    <span id="distance" class="fw-bold"></span>
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-clock me-2"></i>Durée:
                                    <span id="duration" class="fw-bold"></span>
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-money-bill me-2"></i>Prix estimé:
                                    <span id="price" class="fw-bold"></span>
                                </div>
                            </div>
                        </div>


                        <!-- Carte -->
                        <div class="map-container">
                            <div id="map" class="rounded shadow-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .pac-container {
            z-index: 1051 !important;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .pac-item {
            padding: 8px 12px;
            cursor: pointer;
            font-family: inherit;
        }

        .pac-item:hover {
            background-color: #f8f9fa;
        }

        .pac-item-selected {
            background-color: #e9ecef;
        }

        .price-info {
            font-size: 1.1em;
            color: #28a745;
        }


        .map-container {
            position: relative;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        #map {
            height: 500px;
            width: 100%;
            border: 2px solid #dee2e6;
        }

        .input-group-text {
            background-color: #ffffff;
            border-right: none;
        }

        #search {
            border-left: none;
        }

        #search:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.key') }}&libraries=places"></script>
    <script>
        class MapManager {
            constructor() {
                this.directionsService = null;
                this.directionsRenderer = null;
                this.map = null;
                this.defaultCenter = {
                    lat: 6.1375, // Lomé
                    lng: 1.2125
                };
                // Prix par kilomètre (en FCFA)
                this.pricePerKm = 100; // Ajustez selon vos besoins
                this.init();
            }

            init() {
                if (!this.checkApiKey()) return;

                this.directionsService = new google.maps.DirectionsService();
                this.directionsRenderer = new google.maps.DirectionsRenderer();

                this.map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 7,
                    center: this.defaultCenter,
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.TOP_RIGHT
                    }
                });

                this.directionsRenderer.setMap(this.map);
                this.initAutocomplete();
                this.setupEventListeners();
                this.getCurrentLocation();
            }

            checkApiKey() {
                if (!google || !google.maps) {
                    console.error('Google Maps API non chargée');
                    alert('Erreur de chargement de Google Maps. Veuillez vérifier votre configuration.');
                    return false;
                }
                return true;
            }

            setupEventListeners() {
                const calculateButton = document.getElementById('calculateRoute');
                const locationButton = document.getElementById('useCurrentLocation');

                if (calculateButton) {
                    calculateButton.addEventListener('click', () => this.calculateRoute());
                }

                if (locationButton) {
                    locationButton.addEventListener('click', () => this.getCurrentLocation());
                }
            }

            initAutocomplete() {
                const originInput = document.getElementById('origin');
                const destinationInput = document.getElementById('destination');

                if (originInput && destinationInput) {
                    // Options pour l'autocomplétion
                    const options = {
                        bounds: new google.maps.LatLngBounds(
                            new google.maps.LatLng(6.0, 0.5), // Sud-ouest du Togo
                            new google.maps.LatLng(11.5, 2.0) // Nord-est du Togo
                        ),
                        componentRestrictions: {
                            country: 'TG'
                        },
                        fields: ['address_components', 'geometry', 'name', 'formatted_address'],
                        strictBounds: true,
                        types: ['geocode', 'establishment'] // Inclut les établissements et adresses
                    };

                    // Création des objets Autocomplete
                    const originAutocomplete = new google.maps.places.Autocomplete(originInput, options);
                    const destAutocomplete = new google.maps.places.Autocomplete(destinationInput, options);

                    // Écouteurs d'événements pour la sélection
                    originAutocomplete.addListener('place_changed', () => {
                        const place = originAutocomplete.getPlace();
                        if (place.geometry) {
                            this.map.panTo(place.geometry.location);
                            this.map.setZoom(15);
                        }
                    });

                    destAutocomplete.addListener('place_changed', () => {
                        const place = destAutocomplete.getPlace();
                        if (place.geometry) {
                            this.map.panTo(place.geometry.location);
                            this.map.setZoom(15);
                        }
                    });
                }
            }

            async getCurrentLocation() {
                try {
                    const position = await this.getUserPosition();
                    const userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    this.map.setCenter(userLocation);
                    this.map.setZoom(15);

                    new google.maps.Marker({
                        position: userLocation,
                        map: this.map,
                        title: 'Votre position',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                        }
                    });

                    await this.reverseGeocode(userLocation);
                } catch (error) {
                    this.handleError(error);
                }
            }

            getUserPosition() {
                return new Promise((resolve, reject) => {
                    if (!navigator.geolocation) {
                        reject(new Error('La géolocalisation n\'est pas supportée'));
                    }
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    });
                });
            }

            async reverseGeocode(location) {
                try {
                    const response = await fetch('{{ route('map.location') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: location.lat,
                            longitude: location.lng
                        })
                    });

                    const data = await response.json();
                    if (data.success && document.getElementById('origin')) {
                        document.getElementById('origin').value = data.address;
                    }
                } catch (error) {
                    this.handleError(error);
                }
            }

            async calculateRoute() {
                try {
                    const origin = document.getElementById('origin')?.value;
                    const destination = document.getElementById('destination')?.value;

                    if (!origin || !destination) {
                        throw new Error('Veuillez renseigner le point de départ et la destination');
                    }

                    const response = await fetch('{{ route('map.calculate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            origin,
                            destination
                        })
                    });

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message);
                    }

                    await this.displayRoute(origin, destination, data.data);
                } catch (error) {
                    this.handleError(error);
                }
            }

            async displayRoute(origin, destination, routeData) {
                return new Promise((resolve, reject) => {
                    this.directionsService.route({
                        origin: origin,
                        destination: destination,
                        travelMode: google.maps.TravelMode.DRIVING
                    }, (response, status) => {
                        if (status === 'OK') {
                            this.directionsRenderer.setDirections(response);
                            this.updateRouteInfo(routeData);
                            resolve();
                        } else {
                            reject(new Error('Erreur lors de l\'affichage de l\'itinéraire'));
                        }
                    });
                });
            }

            updateRouteInfo(routeData) {
                const routeInfo = document.getElementById('routeInfo');
                const distance = document.getElementById('distance');
                const duration = document.getElementById('duration');
                const price = document.getElementById('price'); // Ajoutez cet élément dans votre HTML

                if (routeInfo && distance && duration) {
                    // Extraction de la distance en kilomètres
                    const distanceText = routeData.distance.text;
                    const distanceValue = parseFloat(distanceText.replace(' km', ''));

                    // Calcul du prix
                    const estimatedPrice = Math.ceil(distanceValue * this.pricePerKm);

                    // Mise à jour de l'affichage
                    distance.textContent = `${distanceText}`;
                    duration.textContent = `${routeData.duration.text}`;
                    price.textContent = `${estimatedPrice.toLocaleString('fr-FR')} FCFA`;

                    routeInfo.classList.remove('d-none');
                }
            }

            handleError(error) {
                console.error('Erreur:', error);
                alert(error.message || 'Une erreur est survenue');
            }

            async displayRoute(origin, destination, routeData) {
                return new Promise((resolve, reject) => {
                    const request = {
                        origin: origin,
                        destination: destination,
                        travelMode: google.maps.TravelMode.DRIVING,
                        provideRouteAlternatives: true, // Affiche les itinéraires alternatifs
                        optimizeWaypoints: true
                    };

                    this.directionsService.route(request, (response, status) => {
                        if (status === 'OK') {
                            this.directionsRenderer.setDirections(response);

                            // Calcul des informations pour tous les itinéraires
                            const routes = response.routes;
                            const shortestRoute = routes.reduce((prev, current) => {
                                const prevDistance = prev.legs[0].distance.value;
                                const currentDistance = current.legs[0].distance.value;
                                return prevDistance < currentDistance ? prev : current;
                            });

                            const routeInfo = {
                                distance: shortestRoute.legs[0].distance,
                                duration: shortestRoute.legs[0].duration
                            };

                            this.updateRouteInfo(routeInfo);
                            resolve();
                        } else {
                            reject(new Error('Erreur lors de l\'affichage de l\'itinéraire'));
                        }
                    });
                });
            }
        }

        // Initialisation de la carte
        document.addEventListener('DOMContentLoaded', () => {
            window.mapManager = new MapManager();
        });
    </script>
@endpush
