<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cette migration crée la table 'annonces' qui est le cœur de la plateforme.
     * Elle stocke toutes les annonces publiées par les utilisateurs avec leurs
     * informations détaillées, relations avec les utilisateurs et catégories.
     */
    public function up(): void
    {
        Schema::create('annonces', function (Blueprint $table) {
            // Clé primaire auto-incrémentée
            $table->id();
            
            // Clé étrangère vers la table users - identifie l'auteur de l'annonce
            // Cette relation est obligatoire car chaque annonce doit avoir un propriétaire
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('ID de l\'utilisateur qui a publié l\'annonce');
            
            // Clé étrangère vers la table categories - catégorise l'annonce
            // Cette relation est obligatoire pour organiser les annonces
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('restrict')
                  ->comment('ID de la catégorie de l\'annonce');
            
            // Titre de l'annonce - obligatoire, limité à 255 caractères
            // C'est le premier élément que voient les utilisateurs
            $table->string('title')->comment('Titre accrocheur de l\'annonce');
            
            // Description détaillée de l'annonce - obligatoire
            // Permet à l'auteur de décrire en détail ce qu'il propose
            $table->text('description')->comment('Description complète de l\'annonce');
            
            // Prix de l'article/service - optionnel car certaines annonces peuvent être gratuites
            // Utilisation de DECIMAL pour une précision monétaire (10 chiffres, 2 décimales)
            $table->decimal('price', 10, 2)->nullable()->comment('Prix en euros (optionnel)');
            
            // Localisation géographique - optionnelle mais utile pour les rencontres
            // Permet aux utilisateurs de filtrer par zone géographique
            $table->string('location')->nullable()->comment('Ville ou région (optionnel)');
            
            // URL de l'image principale - optionnelle
            // Stocke le chemin vers l'image uploadée ou l'URL d'une image externe
            $table->string('image_url')->nullable()->comment('Chemin vers l\'image principale (optionnel)');
            
            // Timestamps automatiques pour created_at et updated_at
            // Permettent de trier par date de publication et de suivre les modifications
            $table->timestamps();
            
            // Index pour optimiser les performances des requêtes fréquentes
            $table->index(['user_id', 'created_at'], 'idx_user_date');
            $table->index(['category_id', 'created_at'], 'idx_category_date');
            $table->index('created_at', 'idx_date');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Cette méthode supprime la table 'annonces' si la migration est annulée.
     * Attention: cela supprimera toutes les annonces de la base de données.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};