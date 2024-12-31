<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'prenom', 'email', 'voiture_id'];


    // Relation : Un chauffeur possède une voiture
    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }
}
