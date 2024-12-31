@extends('layouts.master')
@section('content')
    <div class="container py-5">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-4 text-center fw-bold text-primary mb-4">
                    <i class="fas fa-map-marked-alt me-3"></i>Calculateur d'Itinéraire
                </h1>
            </div>
        </div>

        <!-- Carte principale -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-gradient bg-primary text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-route me-2"></i>Planification d'Itinéraire
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        <form id="routeForm" class="mb-4">
                            <div class="row g-3">
                                <!-- Point de départ -->
                                <div class="col-md-5">
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text border-end-0">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                        </span>
                                        <input id="origin" type="text" class="form-control border-start-0"
                                            placeholder="Point de départ" required>
                                        <button class="btn btn-outline-secondary clear-input" type="button"
                                            title="Effacer">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" type="button" id="useCurrentLocation"
                                            title="Ma position">
                                            <i class="fas fa-location-arrow"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Destination -->
                                <div class="col-md-5">
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text border-end-0">
                                            <i class="fas fa-flag-checkered text-danger"></i>
                                        </span>
                                        <input id="destination" type="text" class="form-control border-start-0"
                                            placeholder="Destination" required>
                                        <button class="btn btn-outline-secondary clear-input" type="button"
                                            title="Effacer">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Bouton de calcul -->
                                <div class="col-md-2">
                                    <button type="submit" id="calculateRoute"
                                        class="btn btn-primary btn-lg w-100 shadow-sm">
                                        <i class="fas fa-calculator me-2"></i>Calculer
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Informations sur l'itinéraire -->
                        <div id="routeInfo" class="alert alert-info shadow-sm d-none mb-4">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-road fa-lg me-2 text-primary"></i>
                                        <span class="fs-5">Distance: <strong id="distance"></strong></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-clock fa-lg me-2 text-warning"></i>
                                        <span class="fs-5">Durée: <strong id="duration"></strong></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-money-bill fa-lg me-2 text-success"></i>
                                        <span class="fs-5">Prix: <strong id="price"></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carte -->
                        <div class="map-container shadow-sm">
                            <div id="map" class="rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Container pour les toasts -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Overlay de chargement -->
    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none">
        <div class="d-flex justify-content-center align-items-center h-100 bg-white bg-opacity-75">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        /* Styles généraux */
        body {
            background-color: var(--light-color);
        }

        .card {
            border-radius: 1rem;
            overflow: hidden;
        }

        /* Styles des inputs */
        .input-group {
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        /* Bouton clear */
        .clear-input {
            display: none;
            z-index: 4;
        }

        .input-group input:not(:placeholder-shown)+.clear-input {
            display: block;
        }

        /* Carte */
        .map-container {
            padding: 1rem;
            background: var(--light-color);
            border-radius: 1rem;
            margin-top: 2rem;
        }

        #map {
            height: 600px;
            width: 100%;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* AutoComplete Google */
        .pac-container {
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            margin-top: 5px;
            z-index: 1051 !important;
        }

        .pac-item {
            padding: 0.75rem 1rem;
            font-family: inherit;
            border: none;
        }

        .pac-item:hover {
            background-color: var(--light-color);
            cursor: pointer;
        }

        .pac-item-selected {
            background-color: rgba(13, 110, 253, 0.1);
        }

        /* Toast notifications */
        .toast {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .toast-success .toast-header {
            background-color: var(--success-color);
            color: white;
        }

        .toast-error .toast-header {
            background-color: var(--danger-color);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .input-group {
                margin-bottom: 1rem;
            }

            #map {
                height: 400px;
            }

            .col-md-2 {
                margin-top: 1rem;
            }

            #routeInfo .row {
                flex-direction: column;
            }

            #routeInfo .col-md-4 {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.key') }}&libraries=places"></script>
    <script>
        $(document).ready(function() {
            class MapManager {
                constructor() {
                    this.map = null;
                    this.directionsService = null;
                    this.directionsRenderer = null;
                    this.placesService = null;
                    this.geocoder = null;
                    this.markers = [];
                    this.currentInfoWindow = null;
                    this.defaultCenter = {
                        lat: 6.1375,
                        lng: 1.2125
                    }; // Lomé, Togo
                    this.pricePerKm = 100; // Prix en FCFA par km
                    this.bounds = new google.maps.LatLngBounds(
                        new google.maps.LatLng(6.0, 0.5), // Sud-ouest du Togo
                        new google.maps.LatLng(11.5, 2.0) // Nord-est du Togo
                    );

                    this.setupAjax();
                    this.init();
                    this.setupEventListeners();
                }

                setupAjax() {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                }

                init() {
                    if (!this.checkGoogleMapsApi()) return;

                    // Initialisation de la carte
                    this.map = new google.maps.Map($('#map')[0], {
                        zoom: 7,
                        center: this.defaultCenter,
                        mapTypeControl: true,
                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                            position: google.maps.ControlPosition.TOP_RIGHT
                        },
                        fullscreenControl: true,
                        streetViewControl: true,
                        zoomControl: true
                    });

                    // Services Google Maps
                    this.directionsService = new google.maps.DirectionsService();
                    this.directionsRenderer = new google.maps.DirectionsRenderer({
                        map: this.map,
                        suppressMarkers: false,
                        preserveViewport: false
                    });
                    this.geocoder = new google.maps.Geocoder();
                    this.placesService = new google.maps.places.PlacesService(this.map);

                    this.initAutocomplete();
                }

                checkGoogleMapsApi() {
                    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                        this.showToast('Erreur de chargement de Google Maps', 'error');
                        return false;
                    }
                    return true;
                }

                initAutocomplete() {
                    const options = {
                        bounds: this.bounds,
                        componentRestrictions: {
                            country: 'TG'
                        },
                        fields: ['address_components', 'geometry', 'name', 'formatted_address'],
                        strictBounds: true,
                        types: ['geocode', 'establishment']
                    };

                    $.each(['origin', 'destination'], (_, fieldId) => {
                        const autocomplete = new google.maps.places.Autocomplete($(`#${fieldId}`)[0],
                            options);

                        autocomplete.addListener('place_changed', () => {
                            const place = autocomplete.getPlace();
                            if (!place.geometry) {
                                this.showToast(`Aucun lieu trouvé pour : ${place.name}`,
                                    'error');
                                return;
                            }

                            if (fieldId === 'origin') {
                                this.updateMap(place.geometry.location, 'origin');
                            }
                        });
                    });
                }

                setupEventListeners() {
                    $('#routeForm').on('submit', (e) => {
                        e.preventDefault();
                        this.calculateRoute();
                    });

                    $('#useCurrentLocation').on('click', () => {
                        this.getCurrentLocation();
                    });

                    $('.clear-input').on('click', function() {
                        $(this).siblings('input').val('').focus();
                    });

                    $(window).on('resize', () => {
                        google.maps.event.trigger(this.map, 'resize');
                    });
                }

                getCurrentLocation() {
                    if (!navigator.geolocation) {
                        this.showToast('La géolocalisation n\'est pas supportée par votre navigateur', 'error');
                        return;
                    }

                    this.showLoading(true);

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            $.ajax({
                                    url: '/map/current-location', // URL modifiée pour correspondre à votre route
                                    method: 'POST',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        latitude: position.coords.latitude,
                                        longitude: position.coords.longitude
                                    },
                                    dataType: 'json'
                                })
                                .done((response) => {
                                    if (response.success) {
                                        $('#origin').val(response.address);
                                        this.updateMap(response.location, 'origin');
                                        this.showToast('Position actuelle récupérée', 'success');
                                    } else {
                                        this.showToast(response.message, 'error');
                                    }
                                })
                                .fail((error) => {
                                    this.handleError(error);
                                })
                                .always(() => {
                                    this.showLoading(false);
                                });
                        },
                        (error) => {
                            this.showLoading(false);
                            let message = 'Erreur de géolocalisation';
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    message = 'Vous avez refusé la géolocalisation';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    message = 'Position non disponible';
                                    break;
                                case error.TIMEOUT:
                                    message = 'Délai d\'attente dépassé';
                                    break;
                            }
                            this.showToast(message, 'error');
                        }, {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                }

                calculateRoute() {
                    const origin = $('#origin').val();
                    const destination = $('#destination').val();

                    if (!origin || !destination) {
                        this.showToast('Veuillez remplir tous les champs', 'error');
                        return;
                    }

                    this.showLoading(true);

                    $.ajax({
                            url: "{{ route('map.calculate') }}",
                            method: 'POST',
                            data: JSON.stringify({
                                origin: origin,
                                destination: destination
                            }),
                            contentType: 'application/json'
                        })
                        .done((response) => {
                            if (response.success) {
                                this.displayRoute(origin, destination, response.data);
                                this.showToast('Itinéraire calculé avec succès', 'success');
                            } else {
                                this.showToast(response.message, 'error');
                            }
                        })
                        .fail((error) => {
                            this.handleError(error);
                        })
                        .always(() => {
                            this.showLoading(false);
                        });
                }

                displayRoute(origin, destination, routeData) {
                    const request = {
                        origin: origin,
                        destination: destination,
                        travelMode: google.maps.TravelMode.DRIVING
                    };

                    this.directionsService.route(request, (response, status) => {
                        if (status === 'OK') {
                            this.directionsRenderer.setDirections(response);
                            this.updateRouteInfo({
                                distance: routeData.distance,
                                duration: routeData.duration
                            });

                            // Ajuster la vue de la carte pour voir tout l'itinéraire
                            const bounds = new google.maps.LatLngBounds(
                                new google.maps.LatLng(routeData.bounds.southwest),
                                new google.maps.LatLng(routeData.bounds.northeast)
                            );
                            this.map.fitBounds(bounds);
                        } else {
                            this.showToast('Erreur lors de l\'affichage de l\'itinéraire', 'error');
                        }
                    });
                }

                updateRouteInfo(routeData) {
                    const distanceKm = parseFloat(routeData.distance.text);
                    const estimatedPrice = Math.ceil(distanceKm * this.pricePerKm);

                    $('#distance').text(routeData.distance.text);
                    $('#duration').text(routeData.duration.text);
                    $('#price').text(`${estimatedPrice.toLocaleString('fr-FR')} FCFA`);

                    $('#routeInfo').removeClass('d-none').fadeIn(300);
                }

                updateMap(location, type) {
                    this.clearMarkers();

                    const marker = new google.maps.Marker({
                        position: location,
                        map: this.map,
                        animation: google.maps.Animation.DROP,
                        icon: type === 'origin' ?
                            'http://maps.google.com/mapfiles/ms/icons/blue-dot.png' :
                            'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                    });

                    this.markers.push(marker);
                    this.map.setCenter(location);
                    this.map.setZoom(15);
                }

                clearMarkers() {
                    $.each(this.markers, (_, marker) => {
                        marker.setMap(null);
                    });
                    this.markers = [];
                }

                showLoading(show) {
                    const $overlay = $('#loadingOverlay');
                    const $button = $('#calculateRoute');

                    if (show) {
                        $overlay.removeClass('d-none');
                        $button.prop('disabled', true)
                            .html('<span class="spinner-border spinner-border-sm"></span> Calcul...');
                    } else {
                        $overlay.addClass('d-none');
                        $button.prop('disabled', false)
                            .html('<i class="fas fa-route"></i> Calculer');
                    }
                }

                showToast(message, type = 'error') {
                    const toast = $(`
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-${type === 'error' ? 'danger' : 'success'} text-white">
                        <strong class="me-auto">${type === 'error' ? 'Erreur' : 'Succès'}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `);

                    $('.toast-container').append(toast);

                    toast.toast({
                        delay: 3000,
                        autohide: true
                    }).toast('show');

                    toast.on('hidden.bs.toast', function() {
                        $(this).remove();
                    });
                }

                handleError(error) {
                    console.error('Erreur:', error);
                    this.showToast(error.responseJSON?.message || error.message || 'Une erreur est survenue');
                }
            }

            // Initialisation de la carte
            if (typeof google !== 'undefined') {
                window.mapManager = new MapManager();
            } else {
                console.error('Google Maps API non chargée');
            }
        });
    </script>
@endpush
