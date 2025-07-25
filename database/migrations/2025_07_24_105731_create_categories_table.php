<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration crée la table 'categories' qui stocke les différentes catégories
     * d'annonces disponibles sur la plateforme. Chaque catégorie a un nom unique
     * et une description optionnelle pour aider les utilisateurs à comprendre
     * le type d'annonces qu'elle contient.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // Clé primaire auto-incrémentée
            $table->id();
            
            // Nom de la catégorie - obligatoire et unique pour éviter les doublons
            // Exemple: "Électronique", "Immobilier", "Véhicules", etc.
            $table->string('name')->unique()->comment('Nom unique de la catégorie');
            
            // Description optionnelle de la catégorie pour donner plus de contexte
            // aux utilisateurs sur le type d'annonces qu'elle contient
            $table->text('description')->nullable()->comment('Description détaillée de la catégorie');
            
            // Timestamps automatiques pour created_at et updated_at
            // Laravel gère automatiquement ces champs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Cette méthode supprime la table 'categories' si la migration est annulée.
     * Utilisée lors du rollback des migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};