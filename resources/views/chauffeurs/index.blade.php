@extends('layouts.master')
@section('content')

    <body>
        <h1>Liste des Chauffeurs</h1>

        <table border="1">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Voiture Assignée</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chauffeurs as $chauffeur)
                    <tr>
                        <td>{{ $chauffeur->nom }}</td>
                        <td>{{ $chauffeur->prenom }}</td>
                        <td>{{ $chauffeur->email }}</td>
                        <td>
                            @if ($chauffeur->voiture)
                                {{ $chauffeur->voiture->marque }} - {{ $chauffeur->voiture->modele }}
                            @else
                                Aucune voiture assignée
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('chauffeurs.show', $chauffeur) }}">Voir</a>
                            <a href="{{ route('chauffeurs.edit', $chauffeur) }}">Modifier</a>
                            <form action="{{ route('chauffeurs.destroy', $chauffeur) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Voulez-vous supprimer ce chauffeur ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('chauffeurs.create') }}">Ajouter un nouveau chauffeur</a>
    </body>
@endsection
