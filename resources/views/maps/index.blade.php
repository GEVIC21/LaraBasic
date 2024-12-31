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
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    <div class="card-body p-4">
                        <!-- Formulaire -->
                        <form id="routeForm" class="mb-4">
                            <div class="row g-3 align-items-center">
                                <!-- Point de départ -->
                                <div class="col-md-6">
                                    <div class="input-group input-group-lg shadow-sm rounded-lg">
                                        <span class="input-group-text border-end-0 bg-primary text-white">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        <input id="origin" type="text" class="form-control border-start-0"
                                            placeholder="Point de départ" required>
                                    </div>
                                </div>

                                <!-- Bouton de récupération de la position actuelle -->
                                <div class="col-md-4 d-flex justify-content-center">
                                    <button class="btn btn-primary btn-lg w-100 shadow-sm rounded-lg" type="button"
                                        id="useCurrentLocation" title="Ma position">
                                        <i class="fas fa-location-arrow me-2"></i>Utiliser ma position
                                    </button>
                                </div>

                                <!-- Conteneur pour les destinations intermédiaires -->
                                <div id="waypoints-container" class="mt-4"></div>

                                <!-- Destination finale -->
                                <div class="col-md-6">
                                    <div class="input-group input-group-lg shadow-sm rounded-lg">
                                        <span class="input-group-text border-end-0 bg-primary text-white">
                                            <i class="fas fa-flag-checkered"></i>
                                        </span>
                                        <input id="destination" type="text" class="form-control border-start-0"
                                            placeholder="Destination finale" required>
                                    </div>
                                </div>

                                <!-- Bouton d'ajout d'une nouvelle destination -->
                                <div class="col-md-4 d-flex justify-content-center">
                                    <button id="add-waypoint" class="btn btn-outline-primary btn-lg  w-100 shadow-sm rounded-lg"  type="button">
                                        <i class="fas fa-plus"></i> Ajouter une destination
                                    </button>
                                </div>


                                <!-- Bouton de calcul -->
                                <div class="col-md-4 d-flex justify-content-center">
                                    <button type="submit" id="calculateRoute"
                                        class="btn btn-success btn-lg w-100 shadow-sm rounded-lg">
                                        <i class="fas fa-calculator me-2"></i>Calculer l'itinéraire
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Informations sur l'itinéraire -->
                        <div id="routeInfo" class="alert alert-info shadow-sm d-none mb-4 p-3">
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
                        <div class="map-container shadow-sm rounded-lg">
                            <div id="map" class="rounded-lg" style="height: 500px;"></div>
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
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        /* Styles généraux */
        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 8px var(--shadow-color);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 16px var(--shadow-color);
        }

        .card-body {
            padding: 30px;
        }

        /* Styles des inputs */
        .input-group {
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .input-group-text {
            background-color: var(--primary-color);
            border-right: none;
            color: #fff;
        }

        .form-control {
            border-left: none;
            border-color: #ced4da;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }

        /* Boutons */
        .btn {
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        /* Alertes */
        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        /* Flexbox et alignements */
        .d-flex {
            display: flex;
        }

        .justify-content-center {
            justify-content: center;
        }

        .align-items-center {
            align-items: center;
        }

        .text-center {
            text-align: center;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .fs-5 {
            font-size: 1.25rem;
        }

        .me-2 {
            margin-right: 0.5rem;
        }

        .me-3 {
            margin-right: 1rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .p-3 {
            padding: 1rem;
        }

        .d-none {
            display: none;
        }

        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem var(--shadow-color) !important;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .bg-white {
            background-color: #fff !important;
        }

        .bg-opacity-75 {
            background-color: rgba(255, 255, 255, 0.75);
        }

        .position-fixed {
            position: fixed;
        }

        .bottom-0 {
            bottom: 0;
        }

        .end-0 {
            right: 0;
        }

        .top-0 {
            top: 0;
        }

        .start-0 {
            left: 0;
        }

        .w-100 {
            width: 100%;
        }

        .h-100 {
            height: 100%;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        /* Styles spécifiques */
        .display-4 {
            font-size: 3.5rem;
            font-weight: 300;
            line-height: 1.2;
        }

        .fw-bold {
            font-weight: 700;
        }

        .toast-container {
            z-index: 1050;
        }

        #loadingOverlay {
            z-index: 1050;
        }

        .map-container {
            margin-top: 20px;
        }

        #map {
            width: 100%;
            height: 500px;
        }


        .waypoint-input {
            margin-bottom: 10px;
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

                    $('#add-waypoint').on('click', () => {
                        this.addWaypoint();
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
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            this.geocoder.geocode({
                                'location': {
                                    lat: latitude,
                                    lng: longitude
                                }
                            }, (results, status) => {
                                if (status === 'OK') {
                                    if (results[0]) {
                                        $('#origin').val(results[0].formatted_address);
                                        this.updateMap(results[0].geometry.location, 'origin');
                                        this.showToast('Position actuelle récupérée', 'success');
                                    } else {
                                        this.showToast('Aucun résultat trouvé', 'error');
                                    }
                                } else {
                                    this.showToast('Erreur de géocodage: ' + status, 'error');
                                }
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

                addWaypoint() {
                    const waypointInput = $(`
                        <div class="col-md-6 waypoint-input">
                            <div class="input-group input-group-lg shadow-sm rounded-lg">
                                <span class="input-group-text border-end-0 bg-primary text-white">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 waypoint" placeholder="Destination intermédiaire" required>
                                <button type="button" class="btn btn-danger remove-waypoint">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `);

                    $('#waypoints-container').append(waypointInput);
                    this.initAutocompleteWaypoint(waypointInput.find('.waypoint'));
                }

                initAutocompleteWaypoint(input) {
                    const options = {
                        bounds: this.bounds,
                        componentRestrictions: {
                            country: 'TG'
                        },
                        fields: ['address_components', 'geometry', 'name', 'formatted_address'],
                        strictBounds: true,
                        types: ['geocode', 'establishment']
                    };

                    const autocomplete = new google.maps.places.Autocomplete(input[0], options);

                    autocomplete.addListener('place_changed', () => {
                        const place = autocomplete.getPlace();
                        if (!place.geometry) {
                            this.showToast(`Aucun lieu trouvé pour : ${place.name}`, 'error');
                            return;
                        }
                    });
                }

                calculateRoute() {
                    const origin = $('#origin').val();
                    const destination = $('#destination').val();
                    const waypoints = [];

                    $('.waypoint').each((_, input) => {
                        const waypoint = $(input).val();
                        if (waypoint) {
                            waypoints.push({
                                location: waypoint,
                                stopover: true
                            });
                        }
                    });

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
                                destination: destination,
                                waypoints: waypoints
                            }),
                            contentType: 'application/json'
                        })
                        .done((response) => {
                            if (response.success) {
                                this.displayRoute(origin, destination, response.data, waypoints);
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

                displayRoute(origin, destination, routeData, waypoints) {
                    const request = {
                        origin: origin,
                        destination: destination,
                        waypoints: waypoints,
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

            // Gestion des boutons de suppression des destinations intermédiaires
            $(document).on('click', '.remove-waypoint', function() {
                $(this).closest('.waypoint-input').remove();
            });
        });
    </script>
@endpush


