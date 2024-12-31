<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use Illuminate\Http\Request;

class VoitureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer toutes les voitures depuis la base de données
        $voitures = Voiture::all();

        // Retourner la vue avec les données des voitures
        return view('voitures.index', compact('voitures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retourner simplement la vue du formulaire de création
        return view('voitures.create', [
            'voiture' => new Voiture() // On passe une nouvelle instance pour la cohérence avec le formulaire
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'marque' => 'required|string|max:255', // La marque est obligatoire
            'modele' => 'required|string|max:255', // Le modèle est obligatoire
            'immatriculation' => 'required|string|unique:voitures,immatriculation|max:20', // Immatriculation unique
            'disponible' => 'required|boolean', // Doit être un booléen (1 ou 0)
        ]);

        // Création de la voiture dans la base de données
        Voiture::create($validatedData);

        // Redirection vers la liste des voitures avec un message de succès
        return redirect()->route('voitures.index')
                         ->with('success', 'La voiture a été créée avec succès.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
