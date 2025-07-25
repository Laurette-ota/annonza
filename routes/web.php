<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Plateforme de Publication d'Annonces
|--------------------------------------------------------------------------
|
| Ce fichier définit toutes les routes web de l'application.
| Les routes sont organisées par fonctionnalité et incluent les middlewares
| appropriés pour la sécurité et l'authentification.
|
*/

/*
|--------------------------------------------------------------------------
| Routes Publiques (Accueil et Navigation)
|--------------------------------------------------------------------------
*/// Route d'accueil - Page principale avec liste des annonces
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route alternative pour l'accueil explicite
Route::get('/accueil', [AnnonceController::class, 'index'])->name('accueil');

/*
|--------------------------------------------------------------------------
| Routes des Annonces (Resource Routes)
|--------------------------------------------------------------------------
*/

// Routes complètes pour les annonces (CRUD)
// Les méthodes index et show sont publiques, le reste nécessite une authentification
Route::resource('annonces', AnnonceController::class);

// Route supplémentaire pour "Mes annonces" (utilisateur connecté)
Route::get('/mes-annonces', [AnnonceController::class, 'myAnnonces'])
    ->middleware('auth')
    ->name('annonces.my-annonces');

/*
|--------------------------------------------------------------------------
| Routes des Catégories
|--------------------------------------------------------------------------
*/

// Routes complètes pour les catégories
// Les méthodes administratives (create, store, edit, update, destroy) nécessitent une authentification
Route::resource('categories', CategoryController::class);

// Routes API pour les catégories (JSON)
Route::prefix('api/categories')->group(function () {
    // Statistiques des catégories (pour tableaux de bord)
    Route::get('/stats', [CategoryController::class, 'stats'])
        ->name('api.categories.stats');
    
    // Recherche de catégories (pour autocomplétion)
    Route::get('/search', [CategoryController::class, 'search'])
        ->name('api.categories.search');
});

/*
|--------------------------------------------------------------------------
| Routes des Favoris
|--------------------------------------------------------------------------
*/

// Toutes les routes des favoris nécessitent une authentification
Route::middleware('auth')->group(function () {
    
    // Liste des favoris de l'utilisateur
    Route::get('/favoris', [FavoriteController::class, 'index'])
        ->name('favorites.index');
    
    // Ajout d'une annonce aux favoris
    Route::post('/favoris/{annonce}', [FavoriteController::class, 'store'])
        ->name('favorites.store');
    
    // Suppression d'une annonce des favoris
    Route::delete('/favoris/{annonce}', [FavoriteController::class, 'destroy'])
        ->name('favorites.destroy');
    
    // Basculement du statut favori (toggle) - Très utile pour AJAX
    Route::post('/favoris/{annonce}/toggle', [FavoriteController::class, 'toggle'])
        ->name('favorites.toggle');
    
    // Vérification du statut favori d'une annonce (API)
    Route::get('/favoris/{annonce}/check', [FavoriteController::class, 'check'])
        ->name('favorites.check');
    
    // Statistiques des favoris de l'utilisateur (API)
    Route::get('/api/favoris/stats', [FavoriteController::class, 'stats'])
        ->name('api.favorites.stats');
});

/*
|--------------------------------------------------------------------------
| Routes d'Authentification et Profil Utilisateur
|--------------------------------------------------------------------------
*/

// Dashboard utilisateur (page d'accueil après connexion)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes du profil utilisateur (Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Routes d'Authentification (Laravel Breeze)
|--------------------------------------------------------------------------
*/

// Inclusion des routes d'authentification générées par Breeze
// Cela inclut : login, register, password reset, email verification, etc.
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Routes de Développement et Debug (à supprimer en production)
|--------------------------------------------------------------------------
*/

// Route pour tester la configuration (à supprimer en production)
Route::get('/test-config', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'app_env' => config('app.env'),
        'database' => config('database.default'),
        'storage_link' => public_path('storage'),
        'storage_exists' => file_exists(public_path('storage')),
    ]);
})->name('test.config');

/*
|--------------------------------------------------------------------------
| Gestion des Erreurs 404 Personnalisées
|--------------------------------------------------------------------------
*/

// Fallback pour les routes non trouvées
// Redirige vers la page d'accueil avec un message d'erreur
Route::fallback(function () {
    return redirect()->route('home')
                   ->with('error', 'La page demandée n\'existe pas.');
});