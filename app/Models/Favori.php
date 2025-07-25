<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Favori
 * 
 * Ce modèle représente la relation many-to-many entre les utilisateurs et les annonces.
 * Il permet de gérer les annonces mises en favoris par les utilisateurs.
 * 
 * @property int $id Identifiant unique du favori
 * @property int $user_id ID de l'utilisateur
 * @property int $annonce_id ID de l'annonce
 * @property \Carbon\Carbon $created_at Date d'ajout aux favoris
 * @property \Carbon\Carbon $updated_at Date de dernière modification
 */
class Favori extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     * Laravel utilise par défaut le nom de la classe au pluriel en snake_case.
     */
    protected $table = 'favoris';

    /**
     * Les attributs qui peuvent être assignés en masse.
     * Cette protection évite les vulnérabilités de mass assignment.
     */
    protected $fillable = [
        'user_id',    // ID de l'utilisateur (obligatoire)
        'annonce_id', // ID de l'annonce (obligatoire)
    ];

    /**
     * Les attributs qui doivent être castés vers des types natifs.
     * Laravel convertira automatiquement ces attributs.
     */
    protected $casts = [
        'created_at' => 'datetime', // Conversion en instance Carbon
        'updated_at' => 'datetime', // Conversion en instance Carbon
    ];

    /**
     * Relation Many-to-One avec le modèle User.
     * Chaque favori appartient à un utilisateur.
     * 
     * @return BelongsTo L'utilisateur propriétaire du favori
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relation Many-to-One avec le modèle Annonce.
     * Chaque favori fait référence à une annonce.
     * 
     * @return BelongsTo L'annonce mise en favori
     */
    public function annonce(): BelongsTo
    {
        return $this->belongsTo(Annonce::class, 'annonce_id', 'id');
    }

    /**
     * Scope pour obtenir les favoris d'un utilisateur spécifique.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId ID de l'utilisateur
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour obtenir les favoris d'une annonce spécifique.
     * Utile pour compter combien d'utilisateurs ont mis une annonce en favori.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $annonceId ID de l'annonce
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAnnonce($query, $annonceId)
    {
        return $query->where('annonce_id', $annonceId);
    }

    /**
     * Scope pour obtenir les favoris avec les relations chargées.
     * Optimise les requêtes en évitant le problème N+1.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['user', 'annonce', 'annonce.category']);
    }

    /**
     * Scope pour obtenir les favoris récents.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days Nombre de jours (par défaut 30)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Méthode statique pour vérifier si une annonce est en favori pour un utilisateur.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $annonceId ID de l'annonce
     * @return bool True si l'annonce est en favori
     */
    public static function exists($userId, $annonceId): bool
    {
        return static::where('user_id', $userId)
                    ->where('annonce_id', $annonceId)
                    ->exists();
    }

    /**
     * Méthode statique pour ajouter une annonce aux favoris.
     * Utilise firstOrCreate pour éviter les doublons grâce à la contrainte unique.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $annonceId ID de l'annonce
     * @return static|null L'instance du favori créé ou existant
     */
    public static function addFavorite($userId, $annonceId)
    {
        try {
            return static::firstOrCreate([
                'user_id' => $userId,
                'annonce_id' => $annonceId,
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur (par exemple, contrainte d'unicité violée),
            // on retourne null pour indiquer que l'ajout a échoué
            return null;
        }
    }

    /**
     * Méthode statique pour supprimer une annonce des favoris.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $annonceId ID de l'annonce
     * @return bool True si la suppression a réussi
     */
    public static function removeFavorite($userId, $annonceId): bool
    {
        return static::where('user_id', $userId)
                    ->where('annonce_id', $annonceId)
                    ->delete() > 0;
    }

    /**
     * Méthode statique pour basculer le statut favori d'une annonce.
     * Ajoute si pas en favori, supprime si déjà en favori.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $annonceId ID de l'annonce
     * @return array Tableau avec 'action' ('added' ou 'removed') et 'success' (bool)
     */
    public static function toggleFavorite($userId, $annonceId): array
    {
        $exists = static::exists($userId, $annonceId);

        if ($exists) {
            // L'annonce est déjà en favori, on la supprime
            $success = static::removeFavorite($userId, $annonceId);
            return [
                'action' => 'removed',
                'success' => $success,
                'message' => $success ? 'Annonce retirée des favoris' : 'Erreur lors de la suppression'
            ];
        } else {
            // L'annonce n'est pas en favori, on l'ajoute
            $favorite = static::addFavorite($userId, $annonceId);
            $success = $favorite !== null;
            return [
                'action' => 'added',
                'success' => $success,
                'message' => $success ? 'Annonce ajoutée aux favoris' : 'Erreur lors de l\'ajout'
            ];
        }
    }

    /**
     * Méthode statique pour obtenir le nombre de favoris d'une annonce.
     * 
     * @param int $annonceId ID de l'annonce
     * @return int Nombre de favoris
     */
    public static function countForAnnonce($annonceId): int
    {
        return static::where('annonce_id', $annonceId)->count();
    }

    /**
     * Méthode statique pour obtenir les annonces favorites d'un utilisateur.
     * Retourne les annonces avec leurs informations complètes.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre maximum d'annonces à retourner
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFavoriteAnnoncesForUser($userId, $limit = null)
    {
        $query = static::where('user_id', $userId)
                      ->with(['annonce', 'annonce.category', 'annonce.user'])
                      ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->pluck('annonce');
    }

    /**
     * Accesseur pour obtenir le temps écoulé depuis l'ajout aux favoris.
     * 
     * @return string Temps écoulé formaté
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}