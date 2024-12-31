<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\Voiture;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer tous les chauffeurs depuis la base de données
        $chauffeurs = Chauffeur::all();

        // Retourner la vue avec les données des chauffeurs
        return view('chauffeurs.index', compact('chauffeurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Récupérer uniquement les voitures disponibles pour l'assignation
        $voituresDisponibles = Voiture::where('disponible', true)->get();
         // Récupérer les voitures disponibles
        // $voitures = Voiture::where('disponible', true)->get();

        // Si aucune voiture n'est disponible, rediriger avec un message
        if ($voituresDisponibles->isEmpty()) {
            return redirect()->route('chauffeurs.index')
                ->with('error', 'Impossible de créer un chauffeur : aucune voiture disponible.');
        }

        // Retourner la vue du formulaire avec la liste des voitures disponibles
        return view('chauffeurs.create', compact('voituresDisponibles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:chauffeurs,email',
        ]);

        // Vérifiez s'il y a une voiture disponible
        $voiture = Voiture::where('disponible', true)->first();

        // Si une voiture est trouvée, attribuez-la au chauffeur
        if ($voiture) {
            $chauffeur = Chauffeur::create([
                'nom' => $validatedData['nom'],
                'prenom' => $validatedData['prenom'],
                'email' => $validatedData['email'],
                'voiture_id' => $voiture->id,
            ]);

            // Marquez la voiture comme non disponible
            $voiture->update(['disponible' => false]);
        } else {
            return back()->with('error', 'Aucune voiture disponible.');
        }

        return redirect()->route('chauffeurs.index')->with('success', 'Chauffeur enregistré et voiture assignée.');
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
