<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur CategoryController
 * 
 * Ce contrôleur gère toutes les opérations liées aux catégories :
 * - Affichage de la liste des catégories
 * - Affichage des annonces d'une catégorie spécifique
 * - Gestion administrative des catégories (création, modification, suppression)
 * 
 * Les fonctions administratives sont protégées et ne sont accessibles
 * qu'aux utilisateurs ayant les droits appropriés.
 */
class CategoryController extends Controller
{
    /**
     * Constructeur du contrôleur.
     * Applique le middleware d'authentification uniquement aux méthodes administratives.
     * Les utilisateurs non connectés peuvent consulter les catégories et leurs annonces.
     */
    public function __construct()
    {
        // Middleware d'authentification seulement pour les actions administratives
        // Les méthodes index et show restent publiques pour la consultation
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Affiche la liste de toutes les catégories avec leurs statistiques.
     * 
     * Cette méthode :
     * - Récupère toutes les catégories avec le nombre d'annonces
     * - Trie les catégories par nom alphabétique
     * - Calcule les statistiques pour chaque catégorie
     * - Affiche une vue en grille des catégories
     * 
     * @return \Illuminate\View\View Vue avec la liste des catégories
     */
    public function index()
    {
        // Récupération de toutes les catégories avec le nombre d'annonces
        // Utilisation de withCount pour optimiser la requête
        $categories = Category::withCount('annonces')
                             ->orderBy('name')  // Tri alphabétique
                             ->get();

        // Calcul des statistiques générales
        $totalCategories = $categories->count();
        $totalAnnonces = $categories->sum('annonces_count');

        // Identification de la catégorie la plus populaire
        $mostPopularCategory = $categories->sortByDesc('annonces_count')->first();

        // Retour de la vue avec les données nécessaires
        return view('categories.index', compact(
            'categories', 
            'totalCategories', 
            'totalAnnonces', 
            'mostPopularCategory'
        ));
    }

    /**
     * Affiche les annonces d'une catégorie spécifique avec filtrage et recherche.
     * 
     * Cette méthode :
     * - Charge la catégorie demandée
     * - Applique les filtres de recherche et de tri
     * - Pagine les résultats
     * - Affiche les annonces de la catégorie
     * 
     * @param Request $request Requête HTTP avec les paramètres de filtrage
     * @param Category $category La catégorie à afficher (injection de dépendance)
     * @return \Illuminate\View\View Vue avec les annonces de la catégorie
     */
    public function show(Request $request, Category $category)
    {
        // Construction de la requête de base pour les annonces de cette catégorie
        // Chargement des relations nécessaires pour éviter le problème N+1
        $query = $category->annonces()
                         ->with(['user', 'category'])
                         ->orderBy('created_at', 'desc');

        // Application du filtre de recherche par mots-clés si fourni
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->search($searchTerm);
        }

        // Application du filtre par localisation si fourni
        if ($request->filled('location')) {
            $location = $request->get('location');
            $query->byLocation($location);
        }

        // Application des filtres de prix si fournis
        if ($request->filled('price_min') || $request->filled('price_max')) {
            $priceMin = $request->get('price_min') ? (float) $request->get('price_min') : null;
            $priceMax = $request->get('price_max') ? (float) $request->get('price_max') : null;
            $query->byPriceRange($priceMin, $priceMax);
        }

        // Application du tri si spécifié
        $sortBy = $request->get('sort', 'recent'); // Par défaut : plus récentes
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
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Exécution de la requête avec pagination
        $annonces = $query->paginate(12)->appends($request->query());

        // Calcul de statistiques pour cette catégorie
        $totalAnnonces = $category->annonces()->count();
        $recentAnnonces = $category->annonces()->where('created_at', '>=', now()->subDays(7))->count();

        // Récupération des catégories similaires (pour navigation)
        $relatedCategories = Category::where('id', '!=', $category->id)
                                   ->withCount('annonces')
                                   ->orderBy('annonces_count', 'desc')
                                   ->limit(5)
                                   ->get();

        // Retour de la vue avec toutes les données nécessaires
        return view('categories.show', compact(
            'category', 
            'annonces', 
            'totalAnnonces', 
            'recentAnnonces', 
            'relatedCategories'
        ));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle catégorie.
     * 
     * Cette méthode est réservée aux administrateurs.
     * Dans une version complète, il faudrait ajouter un middleware admin.
     * 
     * @return \Illuminate\View\View Vue du formulaire de création
     */
    public function create()
    {
        // TODO: Ajouter une vérification des droits administrateur
        // if (!Auth::user()->isAdmin()) {
        //     return redirect()->route('categories.index')
        //                    ->with('error', 'Accès non autorisé.');
        // }

        return view('categories.create');
    }

    /**
     * Enregistre une nouvelle catégorie en base de données.
     * 
     * Cette méthode :
     * - Valide les données entrantes
     * - Vérifie l'unicité du nom de catégorie
     * - Crée la nouvelle catégorie
     * - Redirige avec un message de succès
     * 
     * @param Request $request Requête HTTP contenant les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection après création
     */
    public function store(Request $request)
    {
        // TODO: Vérification des droits administrateur
        
        // Validation complète des données entrantes
        $validatedData = $request->validate([
            'name' => [
                'required',              // Le nom est obligatoire
                'string',               // Doit être une chaîne de caractères
                'max:255',              // Maximum 255 caractères
                'min:2',                // Minimum 2 caractères
                'unique:categories,name', // Le nom doit être unique
            ],
            'description' => [
                'nullable',             // La description est optionnelle
                'string',               // Doit être une chaîne si fournie
                'max:1000',             // Maximum 1000 caractères
            ],
        ], [
            // Messages d'erreur personnalisés en français
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'name.unique' => 'Une catégorie avec ce nom existe déjà.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ]);

        try {
            // Création de la nouvelle catégorie
            $category = Category::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
            ]);

            // Redirection vers la page de la catégorie avec message de succès
            return redirect()->route('categories.show', $category)
                           ->with('success', 'La catégorie a été créée avec succès !');

        } catch (\Exception $e) {
            // En cas d'erreur lors de la création
            return back()->withErrors(['general' => 'Une erreur est survenue lors de la création.'])
                       ->withInput();
        }
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie.
     * 
     * Cette méthode est réservée aux administrateurs.
     * 
     * @param Category $category La catégorie à modifier
     * @return \Illuminate\View\View Vue d'édition
     */
    public function edit(Category $category)
    {
        // TODO: Vérification des droits administrateur
        
        return view('categories.edit', compact('category'));
    }

    /**
     * Met à jour une catégorie existante.
     * 
     * Cette méthode :
     * - Valide les nouvelles données
     * - Vérifie l'unicité du nom (sauf pour la catégorie actuelle)
     * - Met à jour la catégorie
     * - Redirige avec un message de succès
     * 
     * @param Request $request Requête contenant les nouvelles données
     * @param Category $category La catégorie à mettre à jour
     * @return \Illuminate\Http\RedirectResponse Redirection après mise à jour
     */
    public function update(Request $request, Category $category)
    {
        // TODO: Vérification des droits administrateur
        
        // Validation des données avec règle d'unicité excluant la catégorie actuelle
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'unique:categories,name,' . $category->id, // Exclut la catégorie actuelle
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            // Messages d'erreur personnalisés
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'name.unique' => 'Une catégorie avec ce nom existe déjà.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ]);

        try {
            // Mise à jour de la catégorie
            $category->update($validatedData);

            // Redirection avec message de succès
            return redirect()->route('categories.show', $category)
                           ->with('success', 'La catégorie a été mise à jour avec succès !');

        } catch (\Exception $e) {
            // En cas d'erreur lors de la mise à jour
            return back()->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour.'])
                       ->withInput();
        }
    }

    /**
     * Supprime une catégorie.
     * 
     * Cette méthode :
     * - Vérifie que la catégorie n'a pas d'annonces associées
     * - Supprime la catégorie si elle est vide
     * - Redirige avec un message approprié
     * 
     * ATTENTION: La suppression d'une catégorie avec des annonces est bloquée
     * par la contrainte RESTRICT dans la migration.
     * 
     * @param Category $category La catégorie à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection après suppression
     */
    public function destroy(Category $category)
    {
        // TODO: Vérification des droits administrateur
        
        try {
            // Vérification que la catégorie n'a pas d'annonces associées
            $annoncesCount = $category->annonces()->count();
            
            if ($annoncesCount > 0) {
                return back()->with('error', 
                    "Impossible de supprimer cette catégorie car elle contient {$annoncesCount} annonce(s). " .
                    "Veuillez d'abord déplacer ou supprimer les annonces associées."
                );
            }

            // Suppression de la catégorie
            $categoryName = $category->name;
            $category->delete();

            // Redirection vers la liste des catégories avec message de succès
            return redirect()->route('categories.index')
                           ->with('success', "La catégorie '{$categoryName}' a été supprimée avec succès.");

        } catch (\Exception $e) {
            // En cas d'erreur lors de la suppression
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Retourne les statistiques des catégories au format JSON.
     * 
     * Cette méthode est utile pour les tableaux de bord administratifs
     * ou les graphiques dynamiques.
     * 
     * @return \Illuminate\Http\JsonResponse Statistiques au format JSON
     */
    public function stats()
    {
        try {
            // Récupération des catégories avec leurs statistiques
            $categories = Category::withCount('annonces')
                                ->orderBy('annonces_count', 'desc')
                                ->get();

            // Calcul des statistiques générales
            $totalCategories = $categories->count();
            $totalAnnonces = $categories->sum('annonces_count');
            $averageAnnoncesPerCategory = $totalCategories > 0 ? round($totalAnnonces / $totalCategories, 2) : 0;

            // Top 5 des catégories les plus populaires
            $topCategories = $categories->take(5)->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'annonces_count' => $category->annonces_count,
                    'percentage' => $category->annonces_count > 0 ? 
                        round(($category->annonces_count / Category::withCount('annonces')->get()->sum('annonces_count')) * 100, 1) : 0
                ];
            });

            // Catégories vides (sans annonces)
            $emptyCategories = $categories->where('annonces_count', 0)->count();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_categories' => $totalCategories,
                    'total_annonces' => $totalAnnonces,
                    'average_annonces_per_category' => $averageAnnoncesPerCategory,
                    'empty_categories' => $emptyCategories,
                    'top_categories' => $topCategories
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des statistiques.'
            ], 500);
        }
    }

    /**
     * Recherche des catégories par nom.
     * 
     * Cette méthode est utile pour les fonctionnalités d'autocomplétion
     * ou de recherche en temps réel.
     * 
     * @param Request $request Requête contenant le terme de recherche
     * @return \Illuminate\Http\JsonResponse Résultats de recherche au format JSON
     */
    public function search(Request $request)
    {
        // Validation du paramètre de recherche
        $request->validate([
            'q' => 'required|string|min:1|max:255'
        ]);

        try {
            $searchTerm = $request->get('q');

            // Recherche des catégories correspondantes
            $categories = Category::search($searchTerm)
                                ->withCount('annonces')
                                ->orderBy('name')
                                ->limit(10) // Limite à 10 résultats pour l'autocomplétion
                                ->get();

            // Formatage des résultats pour l'API
            $results = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->short_description,
                    'annonces_count' => $category->annonces_count,
                    'url' => route('categories.show', $category)
                ];
            });

            return response()->json([
                'success' => true,
                'results' => $results,
                'total' => $results->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la recherche.'
            ], 500);
        }
    }
}
