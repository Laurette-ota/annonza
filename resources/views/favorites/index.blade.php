{{--
  Vue des favoris de l'utilisateur
  Liste des annonces mises en favori avec gestion et statistiques
  Design avec glassmorphisme et interactions avancées
--}}

@extends('layouts.app')

@section('title', 'Mes favoris (' . $favoris->total() . ')')
@section('description', 'Gérez vos annonces favorites. ' . $favoris->total() . ' annonces sauvegardées.')
@section('keywords', 'favoris, annonces sauvegardées, mes favoris, wishlist')

@push('styles')
<style>
    /* Styles pour les cartes de favoris */
    .favori-card {
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .favori-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    
    /* Animation pour le bouton de suppression des favoris */
    .remove-favorite-btn {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.3);
        transition: all 0.3s ease;
    }
    
    .remove-favorite-btn:hover {
        background: rgba(239, 68, 68, 0.4);
        transform: scale(1.05);
    }
    
    /* Styles pour les statistiques */
    .stat-card {
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }
    
    /* Animation pour les actions de groupe */
    .bulk-actions {
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transform: translateY(-100%);
        transition: all 0.3s ease;
    }
    
    .bulk-actions.show {
        transform: translateY(0);
    }
    
    /* Styles pour les filtres */
    .filter-section {
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .filter-section:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    /* Animation pour l'état vide */
    .empty-state {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Styles pour les checkboxes */
    .custom-checkbox {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.1);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .custom-checkbox:checked {
        background: linear-gradient(135deg, #0ea5e9 0%, #d946ef 100%);
        border-color: #0ea5e9;
    }
    
    .custom-checkbox:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }
    
    /* Animation pour les badges de catégorie */
    .category-badge {
        background: linear-gradient(135deg, rgba(217, 70, 239, 0.2) 0%, rgba(249, 115, 22, 0.2) 100%);
        border: 1px solid rgba(217, 70, 239, 0.3);
        transition: all 0.2s ease;
    }
    
    .category-badge:hover {
        background: linear-gradient(135deg, rgba(217, 70, 239, 0.3) 0%, rgba(249, 115, 22, 0.3) 100%);
        transform: scale(1.05);
    }
    
    /* Styles pour les alertes de prix */
    .price-alert {
        background: rgba(34, 197, 94, 0.2);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #86efac;
    }
    
    /* Animation de pulsation pour les nouvelles annonces */
    .new-listing {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>
@endpush

@section('content')
<div class="pt-20 pb-8">
    
    {{-- En-tête de la page --}}
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
            
            {{-- Titre principal --}}
            <div class="text-center mb-8 fade-in">
                <h1 class="text-4xl font-bold text-white mb-4">
                    <span class="bg-gradient-to-r from-red-400 to-pink-400 bg-clip-text text-transparent">
                        ❤️ Mes favoris
                    </span>
                </h1>
                <p class="text-xl text-white/80 max-w-2xl mx-auto">
                    Retrouvez toutes les annonces que vous avez sauvegardées. 
                    Gérez votre liste et ne ratez aucune opportunité !
                </p>
            </div>

            {{-- Statistiques des favoris --}}
            @if($favoris->total() > 0)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-red-400 mb-2">{{ $favoris->total() }}</div>
                        <div class="text-white/60 text-sm">{{ Str::plural('Favori', $favoris->total()) }}</div>
                    </div>
                    
                    <div class="stat-card rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-primary-400 mb-2">
                            {{ $stats['categories_count'] }}
                        </div>
                        <div class="text-white/60 text-sm">{{ Str::plural('Catégorie', $stats['categories_count']) }}</div>
                    </div>
                    
                    <div class="stat-card rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-secondary-400 mb-2">
                            {{ $stats['prix_moyen'] ? number_format($stats['prix_moyen'], 0, ',', ' ') . '€' : 'N/A' }}
                        </div>
                        <div class="text-white/60 text-sm">Prix moyen</div>
                    </div>
                    
                    <div class="stat-card rounded-2xl p-6 text-center">
                        <div class="text-3xl font-bold text-accent-400 mb-2">
                            {{ $stats['ajouts_recents'] }}
                        </div>
                        <div class="text-white/60 text-sm">Ajoutés cette semaine</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Section des filtres et actions --}}
    @if($favoris->total() > 0)
        <div class="px-4 sm:px-6 lg:px-8 mb-8">
            <div class="max-w-7xl mx-auto">
                <div class="filter-section rounded-2xl p-6">
                    
                    {{-- Barre d'actions de groupe --}}
                    <div id="bulkActions" class="bulk-actions rounded-xl p-4 mb-6 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-white font-medium">
                                    <span id="selectedCount">0</span> {{ Str::plural('élément sélectionné', 0) }}
                                </span>
                                <button onclick="selectAll()" 
                                        class="text-primary-400 hover:text-primary-300 text-sm">
                                    Tout sélectionner
                                </button>
                                <button onclick="deselectAll()" 
                                        class="text-white/60 hover:text-white text-sm">
                                    Tout désélectionner
                                </button>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button onclick="removeSelectedFavorites()" 
                                        class="remove-favorite-btn px-4 py-2 rounded-lg text-white font-medium transition-all duration-300">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Supprimer</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Filtres et recherche --}}
                    <form method="GET" action="{{ route('favoris.index') }}" id="filterForm" class="space-y-4">
                        
                        {{-- Barre de recherche --}}
                        <div class="flex flex-col lg:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <input type="text" 
                                           name="search" 
                                           value="{{ request('search') }}"
                                           placeholder="Rechercher dans mes favoris..."
                                           class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-300 hover:scale-105">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <span>Rechercher</span>
                                </div>
                            </button>
                        </div>

                        {{-- Filtres avancés --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            
                            {{-- Catégorie --}}
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Catégorie</label>
                                <select name="category" 
                                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                                    <option value="" class="bg-gray-800">Toutes les catégories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ request('category') == $category->id ? 'selected' : '' }}
                                                class="bg-gray-800">
                                            {{ $category->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Prix --}}
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Prix max (€)</label>
                                <input type="number" 
                                       name="prix_max" 
                                       value="{{ request('prix_max') }}"
                                       placeholder="Prix maximum"
                                       min="0"
                                       class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            </div>

                            {{-- Tri --}}
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Trier par</label>
                                <select name="sort" 
                                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                                    <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }} class="bg-gray-800">Ajouté récemment</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }} class="bg-gray-800">Prix croissant</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }} class="bg-gray-800">Prix décroissant</option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }} class="bg-gray-800">Titre A-Z</option>
                                </select>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col space-y-2">
                                <button type="submit" 
                                        class="px-4 py-2 bg-gradient-accent hover:bg-gradient-secondary text-white text-sm font-medium rounded-lg transition-all duration-300">
                                    Filtrer
                                </button>
                                <a href="{{ route('favoris.index') }}" 
                                   class="px-4 py-2 glass-subtle border border-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/15 transition-all text-center">
                                    Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Liste des favoris --}}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            @if($favoris->count() > 0)
                {{-- Informations sur les résultats --}}
                <div class="flex justify-between items-center mb-6">
                    <div class="text-white/80">
                        Affichage de {{ $favoris->firstItem() }} à {{ $favoris->lastItem() }} 
                        sur {{ $favoris->total() }} {{ Str::plural('favori', $favoris->total()) }}
                    </div>
                    
                    {{-- Mode sélection --}}
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleSelectionMode()" 
                                id="selectionModeBtn"
                                class="px-4 py-2 glass-subtle border border-white/20 text-white font-medium rounded-lg hover:bg-white/15 transition-all">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Sélectionner</span>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- Grille des favoris --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($favoris as $favori)
                        @php $annonce = $favori->annonce @endphp
                        <div class="favori-card rounded-2xl overflow-hidden fade-in" 
                             data-favori-id="{{ $favori->id }}"
                             data-annonce-id="{{ $annonce->id }}">
                            
                            {{-- Checkbox de sélection --}}
                            <div class="selection-checkbox absolute top-3 left-3 z-10 hidden">
                                <input type="checkbox" 
                                       class="custom-checkbox favori-checkbox" 
                                       value="{{ $favori->id }}"
                                       onchange="updateSelection()">
                            </div>
                            
                            {{-- Image de l'annonce --}}
                            <div class="relative h-48 overflow-hidden" 
                                 onclick="window.location.href='{{ route('annonces.show', $annonce) }}'">
                                @if($annonce->image)
                                    <img src="{{ Storage::url($annonce->image) }}" 
                                         alt="{{ $annonce->titre }}"
                                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                {{-- Badge de prix --}}
                                <div class="absolute top-3 right-3">
                                    <div class="price-tag px-3 py-1 rounded-full text-white font-bold text-sm">
                                        {{ number_format($annonce->prix, 0, ',', ' ') }} €
                                    </div>
                                </div>
                                
                                {{-- Badge "nouveau" si ajouté récemment --}}
                                @if($favori->created_at->diffInDays() <= 3)
                                    <div class="absolute bottom-3 left-3">
                                        <div class="price-alert px-2 py-1 rounded-full text-xs font-bold new-listing">
                                            Nouveau favori
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Contenu de la carte --}}
                            <div class="p-4">
                                
                                {{-- En-tête avec catégorie --}}
                                <div class="flex items-center justify-between mb-2">
                                    <div class="category-badge px-2 py-1 rounded-lg text-xs font-medium text-white">
                                        {{ $annonce->category->nom }}
                                    </div>
                                    
                                    <button onclick="event.stopPropagation(); removeFavorite({{ $annonce->id }})" 
                                            class="text-red-400 hover:text-red-300 transition-colors"
                                            title="Retirer des favoris">
                                        <svg class="w-5 h-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2 hover:text-primary-400 transition-colors cursor-pointer"
                                    onclick="window.location.href='{{ route('annonces.show', $annonce) }}'">
                                    {{ $annonce->titre }}
                                </h3>
                                
                                <p class="text-white/60 text-sm mb-3 line-clamp-2">
                                    {{ Str::limit($annonce->description, 100) }}
                                </p>
                                
                                {{-- Informations de l'annonce --}}
                                <div class="space-y-2 text-sm text-white/60">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $annonce->localisation }}</span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $annonce->user->name }}</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ $annonce->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <div class="flex items-center space-x-1 text-red-400">
                                            <svg class="w-4 h-4" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                            <span>{{ $favori->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Actions rapides --}}
                                <div class="mt-4 pt-4 border-t border-white/20">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('annonces.show', $annonce) }}" 
                                           class="flex-1 px-3 py-2 bg-gradient-primary hover:bg-gradient-secondary text-white text-sm font-medium rounded-lg transition-all duration-300 text-center">
                                            Voir l'annonce
                                        </a>
                                        
                                        <button onclick="shareAnnonce('{{ route('annonces.show', $annonce) }}', '{{ $annonce->titre }}')" 
                                                class="px-3 py-2 glass-subtle border border-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/15 transition-all"
                                                title="Partager">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="flex justify-center">
                    {{ $favoris->appends(request()->query())->links('pagination::tailwind') }}
                </div>

            @else
                {{-- État vide --}}
                <div class="empty-state text-center py-16">
                    <div class="glass-strong rounded-2xl p-12 max-w-md mx-auto">
                        <div class="text-6xl mb-6">💔</div>
                        
                        <h3 class="text-2xl font-bold text-white mb-4">Aucun favori trouvé</h3>
                        
                        @if(request()->hasAny(['search', 'category', 'prix_max']))
                            <p class="text-white/60 mb-6">
                                Aucun favori ne correspond à vos critères de recherche.
                            </p>
                            
                            <div class="space-y-3">
                                <a href="{{ route('favoris.index') }}" 
                                   class="block w-full px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-300">
                                    Voir tous mes favoris
                                </a>
                                
                                <a href="{{ route('home') }}" 
                                   class="block w-full px-6 py-3 glass-subtle border border-white/20 text-white font-medium rounded-xl hover:bg-white/15 transition-all">
                                    Découvrir des annonces
                                </a>
                            </div>
                        @else
                            <p class="text-white/60 mb-6">
                                Vous n'avez pas encore d'annonces favorites. 
                                Explorez les annonces et ajoutez celles qui vous intéressent !
                            </p>
                            
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Découvrir des annonces
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Variables globales
    let selectionMode = false;
    let selectedFavorites = new Set();

    // Gestion du mode sélection
    function toggleSelectionMode() {
        selectionMode = !selectionMode;
        const checkboxes = document.querySelectorAll('.selection-checkbox');
        const bulkActions = document.getElementById('bulkActions');
        const btn = document.getElementById('selectionModeBtn');
        
        if (selectionMode) {
            checkboxes.forEach(cb => cb.classList.remove('hidden'));
            bulkActions.classList.remove('hidden');
            bulkActions.classList.add('show');
            btn.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Annuler</span>
                </div>
            `;
        } else {
            checkboxes.forEach(cb => cb.classList.add('hidden'));
            bulkActions.classList.add('hidden');
            bulkActions.classList.remove('show');
            btn.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Sélectionner</span>
                </div>
            `;
            selectedFavorites.clear();
            updateSelection();
        }
    }

    // Mise à jour de la sélection
    function updateSelection() {
        const checkboxes = document.querySelectorAll('.favori-checkbox:checked');
        selectedFavorites.clear();
        
        checkboxes.forEach(cb => {
            selectedFavorites.add(cb.value);
        });
        
        const count = selectedFavorites.size;
        document.getElementById('selectedCount').textContent = count;
        
        // Mise à jour du texte pluriel
        const countElement = document.getElementById('selectedCount').parentNode;
        const pluralText = count > 1 ? 'éléments sélectionnés' : 'élément sélectionné';
        countElement.innerHTML = countElement.innerHTML.replace(/éléments? sélectionnés?/, pluralText);
    }

    // Sélectionner tout
    function selectAll() {
        const checkboxes = document.querySelectorAll('.favori-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = true;
        });
        updateSelection();
    }

    // Désélectionner tout
    function deselectAll() {
        const checkboxes = document.querySelectorAll('.favori-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        updateSelection();
    }

    // Supprimer les favoris sélectionnés
    async function removeSelectedFavorites() {
        if (selectedFavorites.size === 0) {
            showToast('Aucun élément sélectionné', 'warning');
            return;
        }
        
        const count = selectedFavorites.size;
        if (!confirm(`Êtes-vous sûr de vouloir supprimer ${count} ${count > 1 ? 'favoris' : 'favori'} ?`)) {
            return;
        }
        
        try {
            const promises = Array.from(selectedFavorites).map(async (favoriId) => {
                const card = document.querySelector(`[data-favori-id="${favoriId}"]`);
                const annonceId = card.dataset.annonceId;
                
                const response = await ajaxRequest(`/favoris/${annonceId}`, 'DELETE');
                if (response.ok) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => card.remove(), 300);
                    return true;
                } else {
                    throw new Error('Erreur lors de la suppression');
                }
            });
            
            await Promise.all(promises);
            
            selectedFavorites.clear();
            updateSelection();
            toggleSelectionMode();
            
            showToast(`${count} ${count > 1 ? 'favoris supprimés' : 'favori supprimé'}`, 'success');
            
            // Recharger la page si plus de favoris
            setTimeout(() => {
                if (document.querySelectorAll('.favori-card').length === 0) {
                    window.location.reload();
                }
            }, 1000);
            
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur lors de la suppression', 'error');
        }
    }

    // Supprimer un favori individuel
    async function removeFavorite(annonceId) {
        if (!confirm('Êtes-vous sûr de vouloir retirer cette annonce de vos favoris ?')) {
            return;
        }
        
        try {
            const response = await ajaxRequest(`/favoris/${annonceId}`, 'DELETE');
            const result = await response.json();
            
            if (response.ok) {
                const card = document.querySelector(`[data-annonce-id="${annonceId}"]`);
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                setTimeout(() => card.remove(), 300);
                
                showToast('Favori supprimé', 'success');
                
                // Recharger la page si plus de favoris
                setTimeout(() => {
                    if (document.querySelectorAll('.favori-card').length === 0) {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                showToast(result.message || 'Erreur lors de la suppression', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur de connexion', 'error');
        }
    }

    // Fonction de partage
    function shareAnnonce(url, title) {
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            });
        } else {
            navigator.clipboard.writeText(url).then(() => {
                showToast('Lien copié dans le presse-papiers !', 'success');
            });
        }
    }

    // Soumission automatique du formulaire lors du changement de tri
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.querySelector('select[name="sort"]');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        }
    });

    // Fonction pour afficher les messages toast
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-24 right-4 z-50 glass-strong rounded-xl p-4 text-white transform translate-x-full transition-transform duration-300`;
        
        if (type === 'success') toast.classList.add('border-l-4', 'border-green-500');
        else if (type === 'error') toast.classList.add('border-l-4', 'border-red-500');
        else if (type === 'warning') toast.classList.add('border-l-4', 'border-yellow-500');
        else toast.classList.add('border-l-4', 'border-blue-500');
        
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-white/60 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Animation d'apparition des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observer toutes les cartes de favoris
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.favori-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            card.style.transitionDelay = `${index * 0.1}s`;
            
            observer.observe(card);
        });
    });
</script>
@endpush