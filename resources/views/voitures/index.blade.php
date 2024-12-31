@extends('layouts.master')
@section('content')
    <h1>Liste des Voitures</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Immatriculation</th>
                <th>Disponibilité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($voitures as $voiture)
                <tr>
                    <td>{{ $voiture->marque }}</td>
                    <td>{{ $voiture->modele }}</td>
                    <td>{{ $voiture->immatriculation }}</td>
                    <td>{{ $voiture->disponible ? 'Disponible' : 'Non Disponible' }}</td>
                    <td>
                        <a href="{{ route('voitures.show', $voiture) }}">Voir</a>
                        <a href="{{ route('voitures.edit', $voiture) }}">Modifier</a>
                        <form action="{{ route('voitures.destroy', $voiture) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Voulez-vous supprimer cette voiture ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('voitures.create') }}">Ajouter une nouvelle voiture</a>
@endsection
