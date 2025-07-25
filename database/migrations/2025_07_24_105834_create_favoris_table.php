<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration crée la table 'favoris' qui gère la relation many-to-many
     * entre les utilisateurs et les annonces. Elle permet aux utilisateurs de
     * sauvegarder des annonces qui les intéressent pour les retrouver facilement.
     */
    public function up(): void
    {
        Schema::create('favoris', function (Blueprint $table) {
            // Clé primaire auto-incrémentée
            $table->id();
            
            // Clé étrangère vers la table users - identifie l'utilisateur
            // qui a ajouté l'annonce à ses favoris
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('ID de l\'utilisateur propriétaire du favori');
            
            // Clé étrangère vers la table annonces - identifie l'annonce
            // qui a été ajoutée aux favoris
            $table->foreignId('annonce_id')
                  ->constrained('annonces')
                  ->onDelete('cascade')
                  ->comment('ID de l\'annonce mise en favori');
            
            // Timestamps automatiques pour created_at et updated_at
            // created_at permet de savoir quand l'annonce a été ajoutée aux favoris
            // updated_at est géré automatiquement par Laravel
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter qu'un utilisateur ajoute
            // plusieurs fois la même annonce à ses favoris
            // Cette contrainte composite garantit l'intégrité des données
            $table->unique(['user_id', 'annonce_id'], 'unique_user_annonce_favorite');
            
            // Index pour optimiser les requêtes fréquentes
            // Index sur user_id pour récupérer rapidement tous les favoris d'un utilisateur
            $table->index('user_id', 'idx_favoris_user');
            
            // Index sur annonce_id pour savoir rapidement combien d'utilisateurs
            // ont mis une annonce en favori (statistiques)
            $table->index('annonce_id', 'idx_favoris_annonce');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Cette méthode supprime la table 'favoris' si la migration est annulée.
     * Cela supprimera tous les favoris des utilisateurs mais n'affectera pas
     * les utilisateurs ni les annonces grâce aux relations bien définies.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoris');
    }
};