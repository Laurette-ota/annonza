<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour la page d'accueil de la plateforme d'annonces
 * 
 * Ce contrôleur gère l'affichage de la page d'accueil avec la liste des annonces,
 * les fonctionnalités de recherche et de filtrage, ainsi que les statistiques générales.
 * 
 * Fonctionnalités principales :
 * - Affichage paginé des annonces
 * - Recherche par mots-clés
 * - Filtrage par catégorie, localisation et prix
 * - Tri des résultats
 * - Statistiques de la plateforme
 */
class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil avec la liste des annonces
     * 
     * Cette méthode gère l'affichage principal de la plateforme avec :
     * - Liste paginée des annonces (12 par page)
     * - Système de recherche et filtrage avancé
     * - Statistiques générales de la plateforme
     * - Tri des résultats selon différents critères
     * 
     * @param Request $request Requête HTTP contenant les paramètres de recherche/filtrage
     * @return \Illuminate\View\View Vue de la page d'accueil
     */
    public function index(Request $request)
    {
        // Construction de la requête de base pour les annonces
        // On utilise Eloquent avec eager loading pour optimiser les performances
        $query = Annonce::with(['user', 'category'])
                        ->where('status', 'active'); // Seulement les annonces actives

        // === SYSTÈME DE RECHERCHE ===
        
        // Recherche par mots-clés dans le titre et la description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // === SYSTÈME DE FILTRAGE ===
        
        // Filtrage par catégorie
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Filtrage par localisation
        if ($request->filled('localisation')) {
            $query->where('location', 'LIKE', "%{$request->localisation}%");
        }

        // Filtrage par prix minimum
        if ($request->filled('prix_min')) {
            $query->where('price', '>=', $request->prix_min);
        }

        // Filtrage par prix maximum
        if ($request->filled('prix_max')) {
            $query->where('price', '<=', $request->prix_max);
        }

        // === SYSTÈME DE TRI ===
        
        // Tri des résultats selon le paramètre demandé
        $sortBy = $request->get('sort', 'recent'); // Par défaut : plus récent
        
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // === PAGINATION ===
        
        // Pagination avec conservation des paramètres de recherche
        $annonces = $query->paginate(12)->withQueryString();

        // === DONNÉES COMPLÉMENTAIRES ===
        
        // Récupération de toutes les catégories pour le filtre
        $categories = Category::orderBy('name')->get();

        // === STATISTIQUES GÉNÉRALES ===
        
        // Calcul des statistiques de la plateforme
        $stats = $this->calculatePlatformStats();

        // === ANNONCES MISES EN AVANT ===
        
        // Sélection d'annonces récentes et populaires pour la mise en avant
        $annoncesMisesEnAvant = Annonce::with(['user', 'category'])
                                      ->where('status', 'active')
                                      ->where('created_at', '>=', now()->subDays(7)) // Dernière semaine
                                      ->orderBy('created_at', 'desc')
                                      ->limit(6)
                                      ->get();

        // === CATÉGORIES POPULAIRES ===
        
        // Calcul des catégories les plus actives
        $categoriesPopulaires = Category::withCount(['annonces' => function ($query) {
                                                    $query->where('status', 'active');
                                                }])
                                              ->having('annonces_count', '>', 0)
                                              ->orderBy('annonces_count', 'desc')
                                              ->limit(8)
                                              ->get();

        // === RETOUR DE LA VUE ===
        
        return view('annonces.index', compact(
            'annonces',
            'categories',
            'stats',
            'annoncesMisesEnAvant',
            'categoriesPopulaires'
        ));
    }

    /**
     * Calcule les statistiques générales de la plateforme
     * 
     * Cette méthode privée calcule diverses métriques importantes :
     * - Nombre total d'annonces actives
     * - Nombre d'utilisateurs inscrits
     * - Nombre de catégories avec annonces
     * - Prix moyen des annonces
     * - Nouvelles annonces cette semaine
     * 
     * @return array Tableau associatif contenant toutes les statistiques
     */
    private function calculatePlatformStats()
    {
        // Nombre total d'annonces actives
        $totalAnnonces = Annonce::where('status', 'active')->count();

        // Nombre total d'utilisateurs inscrits
        $totalUtilisateurs = DB::table('users')->count();

        // Nombre de catégories ayant au moins une annonce active
        $totalCategories = Category::whereHas('annonces', function ($query) {
            $query->where('status', 'active');
        })->count();

        // Prix moyen des annonces actives
        $prixMoyen = Annonce::where('status', 'active')->avg('price');

        // Nombre d'annonces créées cette semaine
        $nouvellesAnnonces = Annonce::where('status', 'active')
                                   ->where('created_at', '>=', now()->subDays(7))
                                   ->count();

        // Nombre d'annonces créées aujourd'hui
        $annoncesAujourdhui = Annonce::where('status', 'active')
                                    ->whereDate('created_at', today())
                                    ->count();

        // Prix minimum et maximum pour donner une fourchette
        $prixMin = Annonce::where('status', 'active')->min('price');
        $prixMax = Annonce::where('status', 'active')->max('price');

        // Retour du tableau de statistiques
        return [
            'total_annonces' => $totalAnnonces,
            'total_utilisateurs' => $totalUtilisateurs,
            'total_categories' => $totalCategories,
            'prix_moyen' => $prixMoyen ? round($prixMoyen, 2) : 0,
            'nouvelles_annonces' => $nouvellesAnnonces,
            'annonces_aujourdhui' => $annoncesAujourdhui,
            'prix_min' => $prixMin ?: 0,
            'prix_max' => $prixMax ?: 0,
        ];
    }

    /**
     * API endpoint pour obtenir les statistiques en temps réel
     * 
     * Cette méthode retourne les statistiques de la plateforme au format JSON
     * pour les requêtes AJAX et les mises à jour dynamiques.
     * 
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec les statistiques
     */
    public function getStats()
    {
        try {
            $stats = $this->calculatePlatformStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche rapide d'annonces (pour l'autocomplétion)
     * 
     * Cette méthode fournit des suggestions de recherche en temps réel
     * basées sur les titres des annonces existantes.
     * 
     * @param Request $request Requête contenant le terme de recherche
     * @return \Illuminate\Http\JsonResponse Suggestions au format JSON
     */
    public function searchSuggestions(Request $request)
    {
        // Validation du paramètre de recherche
        $request->validate([
            'q' => 'required|string|min:2|max:50'
        ]);

        $searchTerm = $request->q;

        try {
            // Recherche dans les titres d'annonces actives
            $suggestions = Annonce::where('status', 'active')
                                 ->where('title', 'LIKE', "%{$searchTerm}%")
                                 ->select('title')
                                 ->distinct()
                                 ->limit(10)
                                 ->pluck('title')
                                 ->toArray();

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche de suggestions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}