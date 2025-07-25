{{--
  Vue d'affichage des annonces par catégorie
  Liste filtrée des annonces avec fonctionnalités de recherche et tri
  Design avec glassmorphisme et interactions avancées
--}}

@extends('layouts.app')

@section('title', 'Annonces dans ' . $category->nom . ' - ' . $annonces->total() . ' résultats')
@section('description', 'Découvrez toutes les annonces dans la catégorie ' . $category->nom . '. ' . $annonces->total() . ' annonces disponibles.')
@section('keywords', $category->nom . ', annonces, ' . $category->description . ', marketplace')

@push('styles')
<style>
    /* Styles pour les filtres avancés */
    .filter-section {
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .filter-section:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    /* Styles pour les cartes d'annonces */
    .annonce-card {
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .annonce-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    
    /* Animation pour les boutons de tri */
    .sort-button {
        transition: all 0.2s ease;
    }
    
    .sort-button.active {
        background: rgba(14, 165, 233, 0.3);
        border-color: rgba(14, 165, 233, 0.6);
        color: #0ea5e9;
    }
    
    .sort-button:hover {
        background: rgba(255, 255, 255, 0.15);
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
    
    /* Animation pour les tags de prix */
    .price-tag {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(217, 70, 239, 0.2) 100%);
        border: 1px solid rgba(14, 165, 233, 0.3);
    }
    
    /* Styles pour les badges de catégorie */
    .category-badge {
        background: linear-gradient(135deg, rgba(217, 70, 239, 0.2) 0%, rgba(249, 115, 22, 0.2) 100%);
        border: 1px solid rgba(217, 70, 239, 0.3);
    }
    
    /* Animation pour le bouton favori */
    .favorite-btn {
        transition: all 0.3s ease;
    }
    
    .favorite-btn:hover {
        transform: scale(1.1);
    }
    
    .favorite-btn.favorited {
        color: #ef4444;
        animation: heartbeat 0.6s ease-in-out;
    }
    
    @keyframes heartbeat {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    /* Styles pour les filtres de prix */
    .price-filter {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .price-filter:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(14, 165, 233, 0.6);
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
    }
    
    /* Animation pour les résultats vides */
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
</style>
@endpush

@section('content')
<div class="pt-20 pb-8">
    
    {{-- En-tête de la catégorie --}}
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
            
            {{-- Fil d'Ariane --}}
            <nav class="mb-8">
                <div class="glass-subtle rounded-xl px-4 py-2">
                    <ol class="flex items-center space-x-2 text-sm text-white/70">
                        <li>
                            <a href="{{ route('home') }}" class="hover:text-white transition-colors">
                                Accueil
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-white font-medium">{{ $category->nom }}</span>
                        </li>
                    </ol>
                </div>
            </nav>

            {{-- Titre et description de la catégorie --}}
            <div class="text-center mb-8 fade-in">
                <div class="category-badge inline-flex items-center px-4 py-2 rounded-full text-sm font-medium text-white mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ $category->nom }}
                </div>
                
                <h1 class="text-4xl font-bold text-white mb-4">
                    <span class="bg-gradient-to-r from-primary-400 to-secondary-400 bg-clip-text text-transparent">
                        {{ $annonces->total() }} {{ Str::plural('annonce', $annonces->total()) }}
                    </span>
                </h1>
                
                @if($category->description)
                    <p class="text-xl text-white/80 max-w-3xl mx-auto">
                        {{ $category->description }}
                    </p>
                @endif
            </div>

            {{-- Statistiques de la catégorie --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-primary-400 mb-2">{{ $annonces->total() }}</div>
                    <div class="text-white/60 text-sm">{{ Str::plural('Annonce', $annonces->total()) }}</div>
                </div>
                
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-secondary-400 mb-2">
                        {{ $stats['prix_moyen'] ? number_format($stats['prix_moyen'], 0, ',', ' ') . '€' : 'N/A' }}
                    </div>
                    <div class="text-white/60 text-sm">Prix moyen</div>
                </div>
                
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-accent-400 mb-2">
                        {{ $stats['prix_min'] ? number_format($stats['prix_min'], 0, ',', ' ') . '€' : 'N/A' }}
                    </div>
                    <div class="text-white/60 text-sm">Prix minimum</div>
                </div>
                
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2">
                        {{ $stats['prix_max'] ? number_format($stats['prix_max'], 0, ',', ' ') . '€' : 'N/A' }}
                    </div>
                    <div class="text-white/60 text-sm">Prix maximum</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section des filtres et recherche --}}
    <div class="px-4 sm:px-6 lg:px-8 mb-8">
        <div class="max-w-7xl mx-auto">
            <div class="filter-section rounded-2xl p-6">
                <form method="GET" action="{{ route('categories.show', $category) }}" id="filterForm" class="space-y-6">
                    
                    {{-- Barre de recherche principale --}}
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Rechercher dans {{ $category->nom }}..."
                                       class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-primary hover:bg-gradient-secondary text-white font-medium rounded-xl transition-all duration-300 hover:scale-105">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span>Rechercher</span>
                            </div>
                        </button>
                    </div>

                    {{-- Filtres avancés --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        
                        {{-- Localisation --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Localisation</label>
                            <input type="text" 
                                   name="localisation" 
                                   value="{{ request('localisation') }}"
                                   placeholder="Ville..."
                                   class="w-full px-3 py-2 price-filter rounded-lg text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>

                        {{-- Prix minimum --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Prix min (€)</label>
                            <input type="number" 
                                   name="prix_min" 
                                   value="{{ request('prix_min') }}"
                                   placeholder="0"
                                   min="0"
                                   class="w-full px-3 py-2 price-filter rounded-lg text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>

                        {{-- Prix maximum --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Prix max (€)</label>
                            <input type="number" 
                                   name="prix_max" 
                                   value="{{ request('prix_max') }}"
                                   placeholder="∞"
                                   min="0"
                                   class="w-full px-3 py-2 price-filter rounded-lg text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>

                        {{-- Tri --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Trier par</label>
                            <select name="sort" 
                                    class="w-full px-3 py-2 price-filter rounded-lg text-white focus:outline-none transition-all">
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }} class="bg-gray-800">Plus récent</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }} class="bg-gray-800">Prix croissant</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }} class="bg-gray-800">Prix décroissant</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }} class="bg-gray-800">Titre A-Z</option>
                            </select>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="flex flex-col space-y-2">
                            <button type="submit" 
                                    class="px-4 py-2 bg-gradient-accent hover:bg-gradient-secondary text-white text-sm font-medium rounded-lg transition-all duration-300">
                                Filtrer
                            </button>
                            <a href="{{ route('categories.show', $category) }}" 
                               class="px-4 py-2 glass-subtle border border-white/20 text-white text-sm font-medium rounded-lg hover:bg-white/15 transition-all text-center">
                                Réinitialiser
                            </a>
                        </div>
                    </div>

                    {{-- Tags des filtres actifs --}}
                    @if(request()->hasAny(['search', 'localisation', 'prix_min', 'prix_max']) || request('sort', 'recent') != 'recent')
                        <div class="flex flex-wrap gap-2">
                            <span class="text-white/60 text-sm">Filtres actifs :</span>
                            
                            @if(request('search'))
                                <span class="glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center">
                                    "{{ request('search') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 hover:text-red-400">×</a>
                                </span>
                            @endif
                            
                            @if(request('localisation'))
                                <span class="glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center">
                                    📍 {{ request('localisation') }}
                                    <a href="{{ request()->fullUrlWithQuery(['localisation' => null]) }}" class="ml-2 hover:text-red-400">×</a>
                                </span>
                            @endif
                            
                            @if(request('prix_min'))
                                <span class="glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center">
                                    Min: {{ number_format(request('prix_min'), 0, ',', ' ') }}€
                                    <a href="{{ request()->fullUrlWithQuery(['prix_min' => null]) }}" class="ml-2 hover:text-red-400">×</a>
                                </span>
                            @endif
                            
                            @if(request('prix_max'))
                                <span class="glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center">
                                    Max: {{ number_format(request('prix_max'), 0, ',', ' ') }}€
                                    <a href="{{ request()->fullUrlWithQuery(['prix_max' => null]) }}" class="ml-2 hover:text-red-400">×</a>
                                </span>
                            @endif
                            
                            @if(request('sort', 'recent') != 'recent')
                                <span class="glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center">
                                    Tri: 
                                    @switch(request('sort'))
                                        @case('price_asc') Prix ↑ @break
                                        @case('price_desc') Prix ↓ @break
                                        @case('title') A-Z @break
                                        @default Récent @break
                                    @endswitch
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}" class="ml-2 hover:text-red-400">×</a>
                                </span>
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Liste des annonces --}}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            @if($annonces->count() > 0)
                {{-- Informations sur les résultats --}}
                <div class="flex justify-between items-center mb-6">
                    <div class="text-white/80">
                        Affichage de {{ $annonces->firstItem() }} à {{ $annonces->lastItem() }} 
                        sur {{ $annonces->total() }} {{ Str::plural('résultat', $annonces->total()) }}
                    </div>
                    
                    {{-- Boutons de vue (grille/liste) --}}
                    <div class="flex space-x-2">
                        <button onclick="setView('grid')" 
                                id="gridViewBtn"
                                class="sort-button active px-3 py-2 glass-subtle border border-white/20 rounded-lg text-white hover:bg-white/15 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button onclick="setView('list')" 
                                id="listViewBtn"
                                class="sort-button px-3 py-2 glass-subtle border border-white/20 rounded-lg text-white hover:bg-white/15 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Grille des annonces --}}
                <div id="annoncesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($annonces as $annonce)
                        <div class="annonce-card rounded-2xl overflow-hidden fade-in" 
                             onclick="window.location.href='{{ route('annonces.show', $annonce) }}'">
                            
                            {{-- Image de l'annonce --}}
                            <div class="relative h-48 overflow-hidden">
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
                                <div class="absolute top-3 left-3">
                                    <div class="price-tag px-3 py-1 rounded-full text-white font-bold text-sm">
                                        {{ number_format($annonce->prix, 0, ',', ' ') }} €
                                    </div>
                                </div>
                                
                                {{-- Bouton favori --}}
                                @auth
                                    <button onclick="event.stopPropagation(); toggleFavorite({{ $annonce->id }})" 
                                            id="favorite-btn-{{ $annonce->id }}"
                                            class="favorite-btn absolute top-3 right-3 glass-strong p-2 rounded-full hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                @endauth
                            </div>

                            {{-- Contenu de la carte --}}
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2 hover:text-primary-400 transition-colors">
                                    {{ $annonce->titre }}
                                </h3>
                                
                                <p class="text-white/60 text-sm mb-3 line-clamp-2">
                                    {{ Str::limit($annonce->description, 100) }}
                                </p>
                                
                                <div class="flex items-center justify-between text-sm text-white/60">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $annonce->localisation }}</span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $annonce->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                {{-- Informations du vendeur --}}
                                <div class="mt-3 pt-3 border-t border-white/20">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-gradient-primary rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-white">
                                                {{ substr($annonce->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-white/60 text-sm">{{ $annonce->user->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="flex justify-center">
                    {{ $annonces->appends(request()->query())->links('pagination::tailwind') }}
                </div>

            @else
                {{-- État vide --}}
                <div class="empty-state text-center py-16">
                    <div class="glass-strong rounded-2xl p-12 max-w-md mx-auto">
                        <svg class="w-24 h-24 text-white/40 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        
                        <h3 class="text-2xl font-bold text-white mb-4">Aucune annonce trouvée</h3>
                        
                        @if(request()->hasAny(['search', 'localisation', 'prix_min', 'prix_max']))
                            <p class="text-white/60 mb-6">
                                Aucune annonce ne correspond à vos critères de recherche dans la catégorie "{{ $category->nom }}".
                            </p>
                            
                            <div class="space-y-3">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="block w-full px-6 py-3 bg-gradient-primary hover:bg-gradient-secondary text-white font-medium rounded-xl transition-all duration-300">
                                    Voir toutes les annonces de cette catégorie
                                </a>
                                
                                <a href="{{ route('home') }}" 
                                   class="block w-full px-6 py-3 glass-subtle border border-white/20 text-white font-medium rounded-xl hover:bg-white/15 transition-all">
                                    Retour à l'accueil
                                </a>
                            </div>
                        @else
                            <p class="text-white/60 mb-6">
                                Il n'y a pas encore d'annonces dans la catégorie "{{ $category->nom }}".
                                Soyez le premier à publier !
                            </p>
                            
                            <a href="{{ route('annonces.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-primary hover:bg-gradient-secondary text-white font-medium rounded-xl transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Publier une annonce
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
    // Gestion des vues (grille/liste)
    let currentView = 'grid';

    function setView(view) {
        currentView = view;
        
        // Mise à jour des boutons
        document.getElementById('gridViewBtn').classList.toggle('active', view === 'grid');
        document.getElementById('listViewBtn').classList.toggle('active', view === 'list');
        
        // Mise à jour de la grille
        const grid = document.getElementById('annoncesGrid');
        if (view === 'list') {
            grid.className = 'space-y-4 mb-8';
            // Ici vous pourriez changer le template des cartes pour une vue liste
        } else {
            grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8';
        }
        
        // Sauvegarder la préférence
        localStorage.setItem('annonces_view', view);
    }

    // Restaurer la vue préférée
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('annonces_view') || 'grid';
        setView(savedView);
        
        // Vérifier les favoris
        checkFavorites();
    });

    // Gestion des favoris
    @auth
    async function toggleFavorite(annonceId) {
        try {
            const response = await ajaxRequest(`/favoris/${annonceId}/toggle`, 'POST');
            const result = await response.json();
            
            if (response.ok) {
                const btn = document.getElementById(`favorite-btn-${annonceId}`);
                const icon = btn.querySelector('svg');
                
                if (result.is_favorite) {
                    icon.setAttribute('fill', 'currentColor');
                    btn.classList.add('favorited');
                    showToast('Ajouté aux favoris !', 'success');
                } else {
                    icon.setAttribute('fill', 'none');
                    btn.classList.remove('favorited');
                    showToast('Retiré des favoris', 'info');
                }
            } else {
                showToast(result.message || 'Erreur lors de la mise à jour', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur de connexion', 'error');
        }
    }

    // Vérifier l'état des favoris au chargement
    async function checkFavorites() {
        const favoriteButtons = document.querySelectorAll('[id^="favorite-btn-"]');
        
        for (const btn of favoriteButtons) {
            const annonceId = btn.id.split('-')[2];
            
            try {
                const response = await ajaxRequest(`/favoris/${annonceId}/check`);
                const result = await response.json();
                
                if (result.is_favorite) {
                    const icon = btn.querySelector('svg');
                    icon.setAttribute('fill', 'currentColor');
                    btn.classList.add('favorited');
                }
            } catch (error) {
                console.error('Erreur lors de la vérification du favori:', error);
            }
        }
    }
    @endauth

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

    // Observer toutes les cartes d'annonces
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.annonce-card');
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