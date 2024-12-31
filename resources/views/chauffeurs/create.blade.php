@extends('layouts.master')

@section('content')

    <h1>Ajouter un nouveau chauffeur</h1>

    <a href="{{ route('chauffeurs.index') }}">Retour à la liste des chauffeurs</a>
    <br>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('chauffeurs.store') }}" method="POST">
        @csrf

        <!-- Champ Nom -->
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required>
        </div>

        <!-- Champ Prénom -->
        <div>
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required>
        </div>

        <!-- Champ Email -->
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        <!-- Sélection des voitures -->
        <div>
            <label for="voiture_id">Voiture Disponible</label>
            <select name="voiture_id" id="voiture_id">
                <option value="">-- Sélectionnez une voiture --</option>
                @foreach ($voituresDisponibles as $voiture)
                    <option value="{{ $voiture->id }}">{{ $voiture->marque }} - {{ $voiture->modele }}</option>
                @endforeach
            </select>
        </div>

        <br>
        <button type="submit">Enregistrer le chauffeur</button>
    </form>

@endsection
