@extends('layouts.master')
@section('content')
<h1>Créer une Nouvelle Voiture</h1>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            <!-- Affichage des erreurs de validation -->
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Formulaire pour ajouter une nouvelle voiture -->
<form action="{{ route('voitures.store') }}" method="POST">
    @csrf <!-- Protéger le formulaire avec un token CSRF -->

    <div>
        <label for="marque">Marque</label> <br>
        <input type="text" name="marque" id="marque" value="{{ old('marque') }}" required>
    </div>

    <div>
        <label for="modele">Modèle</label> <br>
        <input type="text" name="modele" id="modele" value="{{ old('modele') }}" required>
    </div>

    <div>
        <label for="immatriculation">Immatriculation</label> <br>
        <input type="text" name="immatriculation" id="immatriculation" value="{{ old('immatriculation') }}" required>
    </div>

    <div>
        <label for="disponible">Disponible</label> <br>
        <select name="disponible" id="disponible">
            <option value="1" {{ old('disponible') == '1' ? 'selected' : '' }}>Oui</option>
            <option value="0" {{ old('disponible') == '0' ? 'selected' : '' }}>Non</option>
        </select>
    </div>

    <br>
    <button type="submit">Enregistrer</button>
</form>

<br>
<a href="{{ route('voitures.index') }}">Annuler et revenir à la liste des voitures</a>


@endsection
