<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Modèle Annonce
 * 
 * Ce modèle représente les annonces publiées par les utilisateurs.
 * Il gère les relations avec les utilisateurs, catégories et favoris,
 * ainsi que les fonctionnalités de recherche et de filtrage.
 * 
 * @property int $id Identifiant unique de l'annonce
 * @property int $user_id ID de l'utilisateur propriétaire
 * @property int $category_id ID de la catégorie
 * @property string $title Titre de l'annonce
 * @property string $description Description détaillée
 * @property float|null $price Prix optionnel
 * @property string|null $location Localisation optionnelle
 * @property string|null $image_url URL de l'image principale
 * @property \Carbon\Carbon $created_at Date de création
 * @property \Carbon\Carbon $updated_at Date de dernière modification
 */
class Annonce extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     * Laravel utilise par défaut le nom de la classe au pluriel en snake_case.
     */
    protected $table = 'annonces';

    /**
     * Les attributs qui peuvent être assignés en masse.
     * Cette protection évite les vulnérabilités de mass assignment.
     */
    protected $fillable = [
        'user_id',     // ID de l'utilisateur propriétaire (obligatoire)
        'category_id', // ID de la catégorie (obligatoire)
        'title',       // Titre de l'annonce (obligatoire)
        'description', // Description détaillée (obligatoire)
        'price',       // Prix optionnel
        'location',    // Localisation optionnelle
        'image_url',   // URL de l'image principale (optionnelle)
    ];

    /**
     * Les attributs qui doivent être castés vers des types natifs.
     * Laravel convertira automatiquement ces attributs.
     */
    protected $casts = [
        'price' => 'decimal:2',     // Conversion en décimal avec 2 décimales
        'created_at' => 'datetime', // Conversion en instance Carbon
        'updated_at' => 'datetime', // Conversion en instance Carbon
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     * Utile pour les APIs où certaines informations ne doivent pas être exposées.
     */
    protected $hidden = [
        // Aucun attribut caché pour le moment
    ];

    /**
     * Relation Many-to-One avec le modèle User.
     * Chaque annonce appartient à un utilisateur.
     * 
     * @return BelongsTo L'utilisateur propriétaire de l'annonce
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relation Many-to-One avec le modèle Category.
     * Chaque annonce appartient à une catégorie.
     * 
     * @return BelongsTo La catégorie de l'annonce
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Relation One-to-Many avec le modèle Favori.
     * Une annonce peut être mise en favori par plusieurs utilisateurs.
     * 
     * @return HasMany Collection des favoris pour cette annonce
     */
    public function favoris(): HasMany
    {
        return $this->hasMany(Favori::class, 'annonce_id', 'id');
    }

    /**
     * Scope pour rechercher des annonces par mots-clés.
     * Recherche dans le titre et la description.
     * 
     * @param Builder $query
     * @param string $search Mots-clés à rechercher
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', '%' . $search . '%')
              ->orWhere('description', 'LIKE', '%' . $search . '%');
        });
    }

    /**
     * Scope pour filtrer par catégorie.
     * 
     * @param Builder $query
     * @param int|null $categoryId ID de la catégorie
     * @return Builder
     */
    public function scopeByCategory($query, $categoryId)
    {
        if (empty($categoryId)) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope pour filtrer par localisation.
     * 
     * @param Builder $query
     * @param string|null $location Localisation à rechercher
     * @return Builder
     */
    public function scopeByLocation($query, $location)
    {
        if (empty($location)) {
            return $query;
        }

        return $query->where('location', 'LIKE', '%' . $location . '%');
    }

    /**
     * Scope pour filtrer par fourchette de prix.
     * 
     * @param Builder $query
     * @param float|null $minPrice Prix minimum
     * @param float|null $maxPrice Prix maximum
     * @return Builder
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    /**
     * Scope pour obtenir les annonces récentes.
     * 
     * @param Builder $query
     * @param int $days Nombre de jours (par défaut 7)
     * @return Builder
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope pour obtenir les annonces avec leurs relations.
     * Optimise les requêtes en chargeant les relations nécessaires.
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['user', 'category', 'favoris']);
    }

    /**
     * Accesseur pour obtenir le prix formaté.
     * Retourne le prix avec le symbole euro ou "Gratuit" si pas de prix.
     * 
     * @return string Prix formaté
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === null || $this->price == 0) {
            return 'Gratuit';
        }

        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    /**
     * Accesseur pour obtenir une description courte.
     * Retourne les 150 premiers caractères de la description.
     * 
     * @return string Description tronquée
     */
    public function getShortDescriptionAttribute(): string
    {
        return strlen($this->description) > 150 
            ? substr($this->description, 0, 150) . '...' 
            : $this->description;
    }

    /**
     * Accesseur pour obtenir le temps écoulé depuis la publication.
     * Retourne une chaîne lisible du type "il y a 2 heures".
     * 
     * @return string Temps écoulé formaté
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Méthode pour vérifier si l'annonce est en favori pour un utilisateur donné.
     * 
     * @param int $userId ID de l'utilisateur
     * @return bool True si l'annonce est en favori
     */
    public function isFavoriteFor($userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->favoris()->where('user_id', $userId)->exists();
    }

    /**
     * Méthode pour obtenir le nombre de favoris de l'annonce.
     * 
     * @return int Nombre de favoris
     */
    public function getFavoritesCount(): int
    {
        return $this->favoris()->count();
    }

    /**
     * Méthode pour obtenir l'URL de l'annonce.
     * 
     * @return string URL de l'annonce
     */
    public function getUrl(): string
    {
        return route('annonces.show', ['annonce' => $this->id]);
    }

    /**
     * Méthode pour vérifier si l'utilisateur peut modifier cette annonce.
     * 
     * @param int $userId ID de l'utilisateur
     * @return bool True si l'utilisateur peut modifier
     */
    public function canBeEditedBy($userId): bool
    {
        return $this->user_id == $userId;
    }

    /**
     * Méthode statique pour obtenir les annonces populaires.
     * Basé sur le nombre de favoris.
     * 
     * @param int $limit Nombre d'annonces à retourner
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopular($limit = 10)
    {
        return static::withCount('favoris')
                    ->orderBy('favoris_count', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Méthode statique pour obtenir les annonces récentes.
     * 
     * @param int $limit Nombre d'annonces à retourner
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecent($limit = 10)
    {
        return static::orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }
}