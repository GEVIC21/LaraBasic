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
    Schema::create('voitures', function (Blueprint $table) {
        $table->id(); // ID unique
        $table->string('marque'); // Exemple Toyota, BMW
        $table->string('modele'); // Exemple Corolla, X3
        $table->string('immatriculation')->unique(); // Immatriculation unique
        $table->boolean('disponible')->default(true); // Indique si la voiture est disponible
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voitures');
    }
};
