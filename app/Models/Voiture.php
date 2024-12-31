<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voiture extends Model
{
    use HasFactory;
    protected $fillable = ['marque', 'modele', 'immatriculation', 'disponible'];

     // Relation : Une voiture peut être attribuée à un chauffeur
     public function chauffeur()
     {
         return $this->hasOne(Chauffeur::class);
     }
}
