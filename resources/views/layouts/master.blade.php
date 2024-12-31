<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            margin-bottom: 60px;
        }

        /* Style moderne pour la navbar */
        .modern-navbar {
            background: linear-gradient(to right, #2c3e50, #3498db);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modern-navbar .navbar-brand {
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .modern-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 10px;
            position: relative;
            transition: all 0.3s ease;
        }

        .modern-navbar .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #fff;
            bottom: 0;
            left: 0;
            transition: width 0.3s ease;
        }

        .modern-navbar .nav-link:hover:after {
            width: 100%;
        }

        .modern-navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }

        .modern-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Style moderne pour le footer */
        .modern-footer {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
            padding: 1.5rem 0;
            margin-top: auto;
        }

        .modern-footer .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
        }

        .modern-footer a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .modern-footer a:hover {
            color: #3498db;
        }

        .modern-footer .social-icons {
            margin-top: 1rem;
        }

        .modern-footer .social-icons a {
            margin: 0 10px;
            font-size: 1.2rem;
        }

        .map-container {
            position: relative;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        #map {
            height: 600px;
            /* Plus grand pour une meilleure visibilité */
            width: 100%;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }

        .route-info {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .current-location-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .current-location-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <header>
        <nav class="navbar navbar-expand-lg modern-navbar">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-car-side me-2"></i>
                    AutoGest
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('voitures.index') }}">
                                <i class="fas fa-car me-1"></i>Voitures
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chauffeurs.index') }}">
                                <i class="fas fa-user-tie me-1"></i>Chauffeurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('map.index') }}">
                                <i class="fas fa-map-marker-alt me-1"></i>Map
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-shrink-0">
        @yield('content')
    </main>

    <footer class="modern-footer">
        <div class="container">
            <div class="footer-content">
                <div class="text-center">
                    <p class="mb-2">© 2024 AutoGest - Tous droits réservés</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                    <p class="mt-2 mb-0">
                        Développé avec <i class="fas fa-heart text-danger"></i> par
                        <a href="" class="fw-bold">Gevic@Coder</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('scripts')
</body>

</html>
