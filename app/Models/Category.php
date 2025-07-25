<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Category
 * 
 * Ce modèle représente les catégories d'annonces dans la base de données.
 * Il gère les relations avec les annonces et fournit des méthodes utilitaires
 * pour organiser et filtrer les annonces par catégorie.
 * 
 * @property int $id Identifiant unique de la catégorie
 * @property string $name Nom unique de la catégorie
 * @property string|null $description Description optionnelle de la catégorie
 * @property \Carbon\Carbon $created_at Date de création
 * @property \Carbon\Carbon $updated_at Date de dernière modification
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     * Laravel utilise par défaut le nom de la classe au pluriel en snake_case,
     * mais nous le spécifions explicitement pour plus de clarté.
     */
    protected $table = 'categories';

    /**
     * Les attributs qui peuvent être assignés en masse.
     * Cette protection évite les vulnérabilités de mass assignment.
     * Seuls ces champs peuvent être remplis via create() ou fill().
     */
    protected $fillable = [
        'name',        // Nom de la catégorie (obligatoire et unique)
        'description', // Description optionnelle de la catégorie
    ];

    /**
     * Les attributs qui doivent être castés vers des types natifs.
     * Laravel convertira automatiquement ces attributs vers les types spécifiés.
     */
    protected $casts = [
        'created_at' => 'datetime', // Conversion automatique en instance Carbon
        'updated_at' => 'datetime', // Conversion automatique en instance Carbon
    ];

    /**
     * Relation One-to-Many avec le modèle Annonce.
     * Une catégorie peut contenir plusieurs annonces.
     * 
     * @return HasMany Collection des annonces appartenant à cette catégorie
     */
    public function annonces(): HasMany
    {
        return $this->hasMany(Annonce::class, 'category_id', 'id')
                    ->orderBy('created_at', 'desc'); // Tri par date de création décroissante
    }

    /**
     * Relation One-to-Many avec le modèle Annonce pour les annonces actives uniquement.
     * Utile pour afficher seulement les annonces valides d'une catégorie.
     * 
     * @return HasMany Collection des annonces actives de cette catégorie
     */
    public function annoncesActives(): HasMany
    {
        return $this->hasMany(Annonce::class, 'category_id', 'id')
                    ->whereNotNull('title') // S'assure que l'annonce a un titre
                    ->whereNotNull('description') // S'assure que l'annonce a une description
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scope pour rechercher des catégories par nom.
     * Permet de filtrer les catégories dont le nom contient une chaîne donnée.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name Nom ou partie du nom à rechercher
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $name)
    {
        return $query->where('name', 'LIKE', '%' . $name . '%');
    }

    /**
     * Scope pour obtenir les catégories avec le nombre d'annonces.
     * Ajoute un champ 'annonces_count' à chaque catégorie.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnnoncesCount($query)
    {
        return $query->withCount('annonces');
    }

    /**
     * Accesseur pour obtenir le nom de la catégorie formaté.
     * Retourne le nom avec la première lettre en majuscule.
     * 
     * @return string Nom formaté de la catégorie
     */
    public function getFormattedNameAttribute(): string
    {
        return ucfirst($this->name);
    }

    /**
     * Accesseur pour obtenir une description courte.
     * Retourne les 100 premiers caractères de la description ou un texte par défaut.
     * 
     * @return string Description courte
     */
    public function getShortDescriptionAttribute(): string
    {
        if (!$this->description) {
            return 'Aucune description disponible.';
        }
        
        return strlen($this->description) > 100 
            ? substr($this->description, 0, 100) . '...' 
            : $this->description;
    }

    /**
     * Méthode pour obtenir l'URL de la catégorie.
     * Génère l'URL pour afficher toutes les annonces de cette catégorie.
     * 
     * @return string URL de la catégorie
     */
    public function getUrl(): string
    {
        return route('categories.show', ['category' => $this->id]);
    }

    /**
     * Méthode statique pour obtenir les catégories les plus populaires.
     * Retourne les catégories triées par nombre d'annonces décroissant.
     * 
     * @param int $limit Nombre maximum de catégories à retourner
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getMostPopular($limit = 10)
    {
        return static::withCount('annonces')
                    ->orderBy('annonces_count', 'desc')
                    ->limit($limit)
                    ->get();
    }
}
