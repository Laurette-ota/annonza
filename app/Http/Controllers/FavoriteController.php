<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Favori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur FavoriteController
 * 
 * Ce contrôleur gère toutes les opérations liées aux favoris :
 * - Ajout d'une annonce aux favoris
 * - Suppression d'une annonce des favoris
 * - Basculement du statut favori (toggle)
 * - Affichage de la liste des favoris de l'utilisateur
 * 
 * Toutes les opérations nécessitent une authentification et incluent
 * une gestion d'erreurs appropriée avec des réponses JSON pour AJAX.
 */
class FavoriteController extends Controller
{
    /**
     * Constructeur du contrôleur.
     * Applique le middleware d'authentification à toutes les méthodes.
     * Les favoris ne sont accessibles qu'aux utilisateurs connectés.
     */
    public function __construct()
    {
        // Middleware d'authentification obligatoire pour toutes les actions
        // Un utilisateur doit être connecté pour gérer ses favoris
        $this->middleware('auth');
    }

    /**
     * Affiche la liste des annonces favorites de l'utilisateur connecté.
     * 
     * Cette méthode :
     * - Récupère toutes les annonces mises en favoris par l'utilisateur
     * - Charge les relations nécessaires (user, category) pour éviter N+1
     * - Trie les favoris par date d'ajout décroissante (plus récents en premier)
     * - Pagine les résultats pour une meilleure performance
     * 
     * @return \Illuminate\View\View Vue avec la liste des favoris
     */
    public function index()
    {
        // Récupération des annonces favorites de l'utilisateur connecté
        // Utilisation de la méthode statique du modèle Favori pour optimiser la requête
        $favoriteAnnonces = Favori::where('user_id', Auth::id())
                                 ->with([
                                     'annonce',              // Chargement de l'annonce
                                     'annonce.user',         // Chargement du propriétaire de l'annonce
                                     'annonce.category'      // Chargement de la catégorie
                                 ])
                                 ->orderBy('created_at', 'desc') // Tri par date d'ajout décroissante
                                 ->paginate(12);                 // 12 favoris par page

        // Extraction des annonces depuis les relations favoris
        // Transformation de la collection pour ne garder que les annonces
        $annonces = $favoriteAnnonces->map(function ($favori) {
            return $favori->annonce;
        });

        // Retour de la vue avec les annonces favorites et les informations de pagination
        return view('favorites.index', [
            'annonces' => $annonces,
            'pagination' => $favoriteAnnonces // Pour conserver les liens de pagination
        ]);
    }

    /**
     * Ajoute une annonce aux favoris de l'utilisateur connecté.
     * 
     * Cette méthode :
     * - Vérifie que l'annonce existe
     * - Vérifie que l'annonce n'est pas déjà en favori
     * - Ajoute l'annonce aux favoris
     * - Retourne une réponse JSON pour les requêtes AJAX
     * 
     * @param Request $request Requête HTTP
     * @param int $annonceId ID de l'annonce à ajouter aux favoris
     * @return JsonResponse|\Illuminate\Http\RedirectResponse Réponse JSON ou redirection
     */
    public function store(Request $request, $annonceId)
    {
        try {
            // Vérification que l'annonce existe dans la base de données
            $annonce = Annonce::findOrFail($annonceId);

            // Vérification que l'utilisateur ne met pas sa propre annonce en favori
            // (optionnel, mais logique métier recommandée)
            if ($annonce->user_id == Auth::id()) {
                $message = 'Vous ne pouvez pas ajouter votre propre annonce aux favoris.';
                
                // Réponse différente selon le type de requête (AJAX ou normale)
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                
                return back()->with('error', $message);
            }

            // Tentative d'ajout aux favoris en utilisant la méthode du modèle
            $favorite = Favori::addFavorite(Auth::id(), $annonceId);

            if ($favorite) {
                $message = 'Annonce ajoutée aux favoris avec succès !';
                
                // Réponse JSON pour les requêtes AJAX
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'action' => 'added',
                        'favorites_count' => Favori::countForAnnonce($annonceId)
                    ]);
                }
                
                // Redirection pour les requêtes normales
                return back()->with('success', $message);
            } else {
                // L'annonce était déjà en favori
                $message = 'Cette annonce est déjà dans vos favoris.';
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 409); // Conflit
                }
                
                return back()->with('info', $message);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // L'annonce n'existe pas
            $message = 'Annonce introuvable.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 404);
            }
            
            return back()->with('error', $message);

        } catch (\Exception $e) {
            // Erreur générale lors de l'ajout
            $message = 'Une erreur est survenue lors de l\'ajout aux favoris.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return back()->with('error', $message);
        }
    }

    /**
     * Supprime une annonce des favoris de l'utilisateur connecté.
     * 
     * Cette méthode :
     * - Vérifie que l'annonce est bien en favori
     * - Supprime l'annonce des favoris
     * - Retourne une réponse appropriée selon le type de requête
     * 
     * @param Request $request Requête HTTP
     * @param int $annonceId ID de l'annonce à supprimer des favoris
     * @return JsonResponse|\Illuminate\Http\RedirectResponse Réponse JSON ou redirection
     */
    public function destroy(Request $request, $annonceId)
    {
        try {
            // Tentative de suppression des favoris
            $removed = Favori::removeFavorite(Auth::id(), $annonceId);

            if ($removed) {
                $message = 'Annonce supprimée des favoris avec succès !';
                
                // Réponse JSON pour les requêtes AJAX
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'action' => 'removed',
                        'favorites_count' => Favori::countForAnnonce($annonceId)
                    ]);
                }
                
                // Redirection pour les requêtes normales
                return back()->with('success', $message);
            } else {
                // L'annonce n'était pas en favori
                $message = 'Cette annonce n\'était pas dans vos favoris.';
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 404);
                }
                
                return back()->with('info', $message);
            }

        } catch (\Exception $e) {
            // Erreur lors de la suppression
            $message = 'Une erreur est survenue lors de la suppression des favoris.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return back()->with('error', $message);
        }
    }

    /**
     * Bascule le statut favori d'une annonce (toggle).
     * 
     * Cette méthode est particulièrement utile pour les interfaces AJAX
     * où un seul bouton permet d'ajouter ou supprimer des favoris.
     * 
     * Si l'annonce est en favori : la supprime
     * Si l'annonce n'est pas en favori : l'ajoute
     * 
     * @param Request $request Requête HTTP
     * @param int $annonceId ID de l'annonce
     * @return JsonResponse|\Illuminate\Http\RedirectResponse Réponse avec le nouvel état
     */
    public function toggle(Request $request, $annonceId)
    {
        try {
            // Vérification que l'annonce existe
            $annonce = Annonce::findOrFail($annonceId);

            // Vérification que l'utilisateur ne manipule pas sa propre annonce
            if ($annonce->user_id == Auth::id()) {
                $message = 'Vous ne pouvez pas ajouter votre propre annonce aux favoris.';
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                
                return back()->with('error', $message);
            }

            // Utilisation de la méthode toggle du modèle Favori
            $result = Favori::toggleFavorite(Auth::id(), $annonceId);

            // Préparation de la réponse selon l'action effectuée
            $statusCode = $result['success'] ? 200 : 500;
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'action' => $result['action'], // 'added' ou 'removed'
                    'is_favorite' => $result['action'] === 'added',
                    'favorites_count' => Favori::countForAnnonce($annonceId)
                ], $statusCode);
            }
            
            // Pour les requêtes normales, redirection avec message approprié
            $flashType = $result['success'] ? 'success' : 'error';
            return back()->with($flashType, $result['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // L'annonce n'existe pas
            $message = 'Annonce introuvable.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 404);
            }
            
            return back()->with('error', $message);

        } catch (\Exception $e) {
            // Erreur générale
            $message = 'Une erreur est survenue lors de la modification des favoris.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return back()->with('error', $message);
        }
    }

    /**
     * Vérifie si une annonce est en favori pour l'utilisateur connecté.
     * 
     * Cette méthode est utile pour les requêtes AJAX qui ont besoin
     * de connaître l'état actuel d'un favori sans effectuer d'action.
     * 
     * @param Request $request Requête HTTP
     * @param int $annonceId ID de l'annonce à vérifier
     * @return JsonResponse Réponse JSON avec l'état du favori
     */
    public function check(Request $request, $annonceId)
    {
        try {
            // Vérification que l'annonce existe
            Annonce::findOrFail($annonceId);

            // Vérification du statut favori
            $isFavorite = Favori::exists(Auth::id(), $annonceId);
            $favoritesCount = Favori::countForAnnonce($annonceId);

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'favorites_count' => $favoritesCount
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce introuvable.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * Retourne les statistiques des favoris pour l'utilisateur connecté.
     * 
     * Cette méthode peut être utilisée pour afficher des informations
     * sur le tableau de bord de l'utilisateur.
     * 
     * @return JsonResponse Statistiques des favoris
     */
    public function stats()
    {
        try {
            $userId = Auth::id();

            // Comptage total des favoris de l'utilisateur
            $totalFavorites = Favori::where('user_id', $userId)->count();

            // Comptage des favoris récents (derniers 30 jours)
            $recentFavorites = Favori::where('user_id', $userId)
                                   ->where('created_at', '>=', now()->subDays(30))
                                   ->count();

            // Catégories les plus favorites
            $topCategories = Favori::where('user_id', $userId)
                                  ->join('annonces', 'favoris.annonce_id', '=', 'annonces.id')
                                  ->join('categories', 'annonces.category_id', '=', 'categories.id')
                                  ->select('categories.name', DB::raw('count(*) as count'))
                                  ->groupBy('categories.id', 'categories.name')
                                  ->orderBy('count', 'desc')
                                  ->limit(5)
                                  ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_favorites' => $totalFavorites,
                    'recent_favorites' => $recentFavorites,
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
}