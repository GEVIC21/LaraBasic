<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chauffeurs', function (Blueprint $table) {
            $table->id(); // ID unique
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->foreignId('voiture_id')
                  ->nullable() // Pas de voiture attribuée par défaut
                  ->constrained('voitures') // Lien avec la table voitures
                  ->nullOnDelete(); // Remet à NULL si la voiture est supprimée
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chauffeurs');
    }
};
