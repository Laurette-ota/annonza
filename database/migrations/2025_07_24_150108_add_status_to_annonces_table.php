<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour ajouter un champ status aux annonces
 * 
 * Cette migration ajoute un champ 'status' à la table 'annonces' pour gérer
 * l'état des annonces (active, inactive, supprimée, etc.)
 */
return new class extends Migration
{
    /**
     * Exécute la migration
     * 
     * Ajoute le champ status avec une valeur par défaut 'active'
     */
    public function up(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            // Ajout du champ status avec valeur par défaut 'active'
            $table->enum('status', ['active', 'inactive', 'deleted'])
                  ->default('active')
                  ->comment('Statut de l\'annonce : active, inactive, deleted');
            
            // Index pour optimiser les requêtes sur le status
            $table->index('status');
        });
    }

    /**
     * Annule la migration
     * 
     * Supprime le champ status de la table annonces
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            // Suppression de l'index
            $table->dropIndex(['status']);
            
            // Suppression du champ status
            $table->dropColumn('status');
        });
    }
};