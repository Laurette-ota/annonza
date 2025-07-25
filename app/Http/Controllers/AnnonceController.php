<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Category;
use App\Models\Favori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Contrôleur AnnonceController
 * 
 * Ce contrôleur gère toutes les opérations CRUD (Create, Read, Update, Delete)
 * pour les annonces. Il inclut également la fonctionnalité de recherche et de filtrage.
 * Toutes les méthodes incluent une gestion d'erreurs appropriée et des validations.
 */
class AnnonceController extends Controller
{
    /**
     * Constructeur du contrôleur.
     * Applique le middleware d'authentification à toutes les méthodes sauf index et show.
     * Les utilisateurs non connectés peuvent voir les annonces mais pas les modifier.
     */
    public function __construct()
    {
        // Middleware d'authentification pour toutes les actions sauf la consultation
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Affiche la liste des annonces avec possibilité de recherche et filtrage.
     * 
     * Cette méthode gère :
     * - La pagination des résultats
     * - La recherche par mots-clés dans le titre et la description
     * - Le filtrage par catégorie
     * - Le filtrage par localisation
     * - Le filtrage par fourchette de prix
     * - Le tri par date de création (plus récentes en premier)
     * 
     * @param Request $request Requête HTTP contenant les paramètres de recherche
     * @return \Illuminate\View\View Vue avec la liste des annonces
     */
    public function index(Request $request)
    {
        // Construction de la requête de base avec les relations nécessaires
        // Utilisation d'Eager Loading pour éviter le problème N+1
        $query = Annonce::with(['user', 'category'])
                        ->orderBy('created_at', 'desc');

        // Application du filtre de recherche par mots-clés
        // Recherche dans le titre et la description de l'annonce
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->search($searchTerm);
        }

        // Application du filtre par catégorie
        // Permet aux utilisateurs de voir seulement les annonces d'une catégorie spécifique
        if ($request->filled('category_id')) {
            $categoryId = $request->get('category_id');
            $query->byCategory($categoryId);
        }

        // Application du filtre par localisation
        // Recherche partielle dans le champ location
        if ($request->filled('location')) {
            $location = $request->get('location');
            $query->byLocation($location);
        }

        // Application des filtres de prix (minimum et maximum)
        // Permet de définir une fourchette de prix
        if ($request->filled('price_min') || $request->filled('price_max')) {
            $priceMin = $request->get('price_min') ? (float) $request->get('price_min') : null;
            $priceMax = $request->get('price_max') ? (float) $request->get('price_max') : null;
            $query->byPriceRange($priceMin, $priceMax);
        }

        // Exécution de la requête avec pagination
        // 12 annonces par page pour un affichage optimal en grille
        $annonces = $query->paginate(12)->appends($request->query());

        // Récupération de toutes les catégories pour le formulaire de filtrage
        // Triées par nom pour une meilleure expérience utilisateur
        $categories = Category::orderBy('name')->get();

        // Retour de la vue avec les données nécessaires
        return view('annonces.index', compact('annonces', 'categories'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle annonce.
     * 
     * Cette méthode prépare les données nécessaires pour le formulaire :
     * - Liste des catégories disponibles
     * - Formulaire vide prêt à être rempli
     * 
     * @return \Illuminate\View\View Vue du formulaire de création
     */
    public function create()
    {
        // Récupération de toutes les catégories pour le select du formulaire
        // Triées par nom pour faciliter la sélection
        $categories = Category::orderBy('name')->get();

        // Retour de la vue de création avec les catégories
        return view('annonces.create', compact('categories'));
    }

    /**
     * Enregistre une nouvelle annonce en base de données.
     * 
     * Cette méthode :
     * - Valide toutes les données entrantes
     * - Gère l'upload d'image si fournie
     * - Associe automatiquement l'annonce à l'utilisateur connecté
     * - Redirige vers la page de détail de l'annonce créée
     * 
     * @param Request $request Requête HTTP contenant les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page de l'annonce
     */
    public function store(Request $request)
    {
        // Validation complète des données entrantes
        // Chaque règle de validation est expliquée pour la sécurité et l'intégrité
        $validatedData = $request->validate([
            'title' => [
                'required',              // Le titre est obligatoire
                'string',               // Doit être une chaîne de caractères
                'max:255',              // Maximum 255 caractères (limite de la BDD)
                'min:5',                // Minimum 5 caractères pour un titre significatif
            ],
            'description' => [
                'required',             // La description est obligatoire
                'string',               // Doit être une chaîne de caractères
                'min:20',               // Minimum 20 caractères pour une description utile
                'max:5000',             // Maximum 5000 caractères pour éviter les abus
            ],
            'category_id' => [
                'required',             // La catégorie est obligatoire
                'integer',              // Doit être un entier
                'exists:categories,id', // Doit exister dans la table categories
            ],
            'price' => [
                'nullable',             // Le prix est optionnel
                'numeric',              // Doit être un nombre si fourni
                'min:0',                // Ne peut pas être négatif
                'max:999999.99',        // Maximum défini par la structure BDD
            ],
            'location' => [
                'nullable',             // La localisation est optionnelle
                'string',               // Doit être une chaîne si fournie
                'max:255',              // Maximum 255 caractères
            ],
            'image' => [
                'nullable',             // L'image est optionnelle
                'image',                // Doit être un fichier image
                'mimes:jpeg,png,jpg,gif', // Formats acceptés
                'max:2048',             // Maximum 2MB
            ],
        ], [
            // Messages d'erreur personnalisés en français
            'title.required' => 'Le titre est obligatoire.',
            'title.min' => 'Le titre doit contenir au moins 5 caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'description.max' => 'La description ne peut pas dépasser 5000 caractères.',
            'category_id.required' => 'Veuillez sélectionner une catégorie.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix ne peut pas être négatif.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format JPEG, PNG, JPG ou GIF.',
            'image.max' => 'L\'image ne peut pas dépasser 2MB.',
        ]);

        // Gestion de l'upload d'image si un fichier a été fourni
        $imageUrl = null;
        if ($request->hasFile('image')) {
            try {
                // Stockage de l'image dans le dossier public/images/annonces
                // Le nom du fichier est généré automatiquement pour éviter les conflits
                $imagePath = $request->file('image')->store('images/annonces', 'public');
                $imageUrl = $imagePath;
            } catch (\Exception $e) {
                // En cas d'erreur lors de l'upload, on retourne avec un message d'erreur
                return back()->withErrors(['image' => 'Erreur lors de l\'upload de l\'image.'])
                           ->withInput();
            }
        }

        try {
            // Création de l'annonce avec les données validées
            // L'ID de l'utilisateur connecté est automatiquement ajouté
            $annonce = Annonce::create([
                'user_id' => Auth::id(),                    // ID de l'utilisateur connecté
                'category_id' => $validatedData['category_id'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'] ?? null, // null si pas de prix
                'location' => $validatedData['location'] ?? null,
                'image_url' => $imageUrl,
            ]);

            // Redirection vers la page de détail de l'annonce avec message de succès
            return redirect()->route('annonces.show', $annonce)
                           ->with('success', 'Votre annonce a été publiée avec succès !');

        } catch (\Exception $e) {
            // En cas d'erreur lors de la création, suppression de l'image uploadée
            if ($imageUrl) {
                Storage::disk('public')->delete($imageUrl);
            }

            // Retour avec message d'erreur
            return back()->withErrors(['general' => 'Une erreur est survenue lors de la publication.'])
                       ->withInput();
        }
    }

    /**
     * Affiche le détail d'une annonce spécifique.
     * 
     * Cette méthode :
     * - Charge l'annonce avec toutes ses relations
     * - Vérifie si l'annonce est en favori pour l'utilisateur connecté
     * - Compte le nombre total de favoris
     * - Charge les annonces similaires de la même catégorie
     * 
     * @param Annonce $annonce L'annonce à afficher (injection de dépendance)
     * @return \Illuminate\View\View Vue de détail de l'annonce
     */
    public function show(Annonce $annonce)
    {
        // Chargement des relations nécessaires pour éviter les requêtes supplémentaires
        $annonce->load(['user', 'category', 'favoris']);

        // Vérification si l'annonce est en favori pour l'utilisateur connecté
        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = $annonce->isFavoriteFor(Auth::id());
        }

        // Comptage du nombre total de favoris pour cette annonce
        $favoritesCount = $annonce->getFavoritesCount();

        // Récupération d'annonces similaires (même catégorie, excluant l'annonce actuelle)
        // Limitées à 4 pour ne pas surcharger la page
        $similarAnnonces = Annonce::where('category_id', $annonce->category_id)
                                 ->where('id', '!=', $annonce->id)
                                 ->with(['user', 'category'])
                                 ->orderBy('created_at', 'desc')
                                 ->limit(4)
                                 ->get();

        // Retour de la vue avec toutes les données nécessaires
        return view('annonces.show', compact('annonce', 'isFavorite', 'favoritesCount', 'similarAnnonces'));
    }

    /**
     * Affiche le formulaire d'édition d'une annonce.
     * 
     * Cette méthode vérifie que l'utilisateur connecté est bien le propriétaire
     * de l'annonce avant d'autoriser la modification.
     * 
     * @param Annonce $annonce L'annonce à modifier
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse Vue d'édition ou redirection
     */
    public function edit(Annonce $annonce)
    {
        // Vérification que l'utilisateur connecté est le propriétaire de l'annonce
        if (!$annonce->canBeEditedBy(Auth::id())) {
            return redirect()->route('annonces.index')
                           ->with('error', 'Vous n\'êtes pas autorisé à modifier cette annonce.');
        }

        // Récupération des catégories pour le formulaire de modification
        $categories = Category::orderBy('name')->get();

        // Retour de la vue d'édition avec l'annonce et les catégories
        return view('annonces.edit', compact('annonce', 'categories'));
    }

    /**
     * Met à jour une annonce existante.
     * 
     * Cette méthode :
     * - Vérifie les autorisations de modification
     * - Valide les nouvelles données
     * - Gère le remplacement d'image si nécessaire
     * - Met à jour l'annonce en base de données
     * 
     * @param Request $request Requête contenant les nouvelles données
     * @param Annonce $annonce L'annonce à mettre à jour
     * @return \Illuminate\Http\RedirectResponse Redirection après mise à jour
     */
    public function update(Request $request, Annonce $annonce)
    {
        // Vérification des autorisations de modification
        if (!$annonce->canBeEditedBy(Auth::id())) {
            return redirect()->route('annonces.index')
                           ->with('error', 'Vous n\'êtes pas autorisé à modifier cette annonce.');
        }

        // Validation des données (mêmes règles que pour la création)
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255', 'min:5'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            // Messages d'erreur personnalisés (mêmes que pour create)
            'title.required' => 'Le titre est obligatoire.',
            'title.min' => 'Le titre doit contenir au moins 5 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'category_id.required' => 'Veuillez sélectionner une catégorie.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne peut pas dépasser 2MB.',
        ]);

        try {
            // Gestion du remplacement d'image
            if ($request->hasFile('image')) {
                // Suppression de l'ancienne image si elle existe
                if ($annonce->image_url) {
                    Storage::disk('public')->delete($annonce->image_url);
                }

                // Upload de la nouvelle image
                $imagePath = $request->file('image')->store('images/annonces', 'public');
                $validatedData['image_url'] = $imagePath;
            }

            // Mise à jour de l'annonce avec les nouvelles données
            $annonce->update($validatedData);

            // Redirection avec message de succès
            return redirect()->route('annonces.show', $annonce)
                           ->with('success', 'Votre annonce a été mise à jour avec succès !');

        } catch (\Exception $e) {
            // En cas d'erreur, retour avec message d'erreur
            return back()->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour.'])
                       ->withInput();
        }
    }

    /**
     * Supprime une annonce.
     * 
     * Cette méthode :
     * - Vérifie les autorisations de suppression
     * - Supprime l'image associée si elle existe
     * - Supprime l'annonce de la base de données
     * - Les favoris sont automatiquement supprimés grâce aux contraintes CASCADE
     * 
     * @param Annonce $annonce L'annonce à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection après suppression
     */
    public function destroy(Annonce $annonce)
    {
        // Vérification des autorisations de suppression
        if (!$annonce->canBeEditedBy(Auth::id())) {
            return redirect()->route('annonces.index')
                           ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette annonce.');
        }

        try {
            // Suppression de l'image associée si elle existe
            if ($annonce->image_url) {
                Storage::disk('public')->delete($annonce->image_url);
            }

            // Suppression de l'annonce
            // Les favoris associés sont automatiquement supprimés grâce à la contrainte CASCADE
            $annonce->delete();

            // Redirection vers la liste des annonces avec message de succès
            return redirect()->route('annonces.index')
                           ->with('success', 'Votre annonce a été supprimée avec succès.');

        } catch (\Exception $e) {
            // En cas d'erreur lors de la suppression
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Affiche les annonces de l'utilisateur connecté.
     * 
     * Cette méthode permet à un utilisateur de voir toutes ses annonces
     * avec la possibilité de les modifier ou supprimer.
     * 
     * @return \Illuminate\View\View Vue avec les annonces de l'utilisateur
     */
    public function myAnnonces()
    {
        // Récupération des annonces de l'utilisateur connecté
        // Triées par date de création décroissante avec pagination
        $annonces = Annonce::where('user_id', Auth::id())
                          ->with(['category'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        // Retour de la vue avec les annonces de l'utilisateur
        return view('annonces.my-annonces', compact('annonces'));
    }
}