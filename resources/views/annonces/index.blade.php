{{--
  Vue d'accueil - Liste des annonces avec recherche et filtres
  Cette vue affiche toutes les annonces avec possibilité de recherche et filtrage
  Design avec glassmorphisme et responsive
--}}

@extends('layouts.app')

@section('title', 'Accueil - Découvrez nos annonces')
@section('description', 'Parcourez notre sélection d\'annonces variées. Trouvez ce que vous cherchez grâce à notre système de recherche et de filtrage avancé.')
@section('keywords', 'annonces, recherche, catégories, vente, achat, services')

@push('styles')
<style>
    /* Styles personnalisés pour les cartes d'annonces */
    .annonce-card {
        transition: all 0.3s ease;
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .annonce-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        background: rgba(255, 255, 255, 0.15);
    }
    
    /* Animation pour les éléments de filtre */
    .filter-tag {
        transition: all 0.2s ease;
    }
    
    .filter-tag:hover {
        transform: scale(1.05);
    }
    
    /* Effet de chargement pour les images */
    .image-loading {
        background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

{{-- Contenu principal de la page --}}
@section('content')
<div class="pt-20 pb-8"> {{-- Padding pour compenser la navigation fixe --}}
    
    {{-- Section héro avec recherche principale --}}
    <section class="relative py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            
            {{-- Titre principal avec animation --}}
            <div class="fade-in">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    <span class="bg-gradient-to-r from-primary-400 via-secondary-400 to-accent-400 bg-clip-text text-transparent">
                        Découvrez
                    </span>
                    <br>
                    <span class="text-white">nos annonces</span>
                </h1>
                <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
                    Trouvez exactement ce que vous cherchez parmi des milliers d'annonces. 
                    Achetez, vendez, échangez en toute simplicité.
                </p>
            </div>

            {{-- Barre de recherche principale --}}
            <div class="fade-in fade-in-delay-1 max-w-4xl mx-auto">
                <form action="{{ route('home') }}" method="GET" class="glass-strong rounded-2xl p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        {{-- Champ de recherche par mots-clés --}}
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-white mb-2">
                                Que recherchez-vous ?
                            </label>
                            <input type="text" 
                                   id="search"
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Ex: iPhone, Voiture, Appartement..." 
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Filtre par catégorie --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-white mb-2">
                                Catégorie
                            </label>
                            <select id="category" 
                                    name="category_id" 
                                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}
                                            class="bg-gray-800 text-white">
                                        {{ $category->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bouton de recherche --}}
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="w-full bg-gradient-primary hover:bg-gradient-secondary px-6 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-glow-primary">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <span>Rechercher</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Filtres avancés (localisation, prix) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-white/20">
                        
                        {{-- Filtre par localisation --}}
                        <div>
                            <label for="location" class="block text-sm font-medium text-white mb-2">
                                Localisation
                            </label>
                            <input type="text" 
                                   id="location"
                                   name="localisation" 
                                   value="{{ request('localisation') }}"
                                   placeholder="Ex: Paris, Lyon, Marseille..." 
                                   class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Filtre prix minimum --}}
                        <div>
                            <label for="prix_min" class="block text-sm font-medium text-white mb-2">
                                Prix minimum (€)
                            </label>
                            <input type="number" 
                                   id="prix_min"
                                   name="prix_min" 
                                   value="{{ request('prix_min') }}"
                                   placeholder="0" 
                                   min="0"
                                   class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Filtre prix maximum --}}
                        <div>
                            <label for="prix_max" class="block text-sm font-medium text-white mb-2">
                                Prix maximum (€)
                            </label>
                            <input type="number" 
                                   id="prix_max"
                                   name="prix_max" 
                                   value="{{ request('prix_max') }}"
                                   placeholder="Illimité" 
                                   min="0"
                                   class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                </form>
            </div>

            {{-- Statistiques rapides --}}
            <div class="fade-in fade-in-delay-2 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="glass-subtle rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ $stats['total_annonces'] }}</div>
                    <div class="text-sm text-white/60">Annonces</div>
                </div>
                <div class="glass-subtle rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ $categories->count() }}</div>
                    <div class="text-sm text-white/60">Catégories</div>
                </div>
                <div class="glass-subtle rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ $stats['total_utilisateurs'] }}</div>
                    <div class="text-sm text-white/60">Utilisateurs</div>
                </div>
                <div class="glass-subtle rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-white">{{ $stats['annonces_aujourdhui'] }}</div>
                    <div class="text-sm text-white/60">Aujourd'hui</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section des résultats --}}
    <section class="px-4 sm:px-6 lg:px-8 pb-16">
        <div class="max-w-7xl mx-auto">
            
            {{-- En-tête des résultats avec filtres actifs --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                
                {{-- Titre et nombre de résultats --}}
                <div class="mb-4 md:mb-0">
                    <h2 class="text-2xl font-bold text-white mb-2">
                        @if(request()->hasAny(['search', 'category_id', 'localisation', 'prix_min', 'prix_max']))
                            Résultats de recherche
                        @else
                            Toutes les annonces
                        @endif
                    </h2>
                    <p class="text-white/60">
                        {{ $annonces->total() }} {{ $annonces->total() > 1 ? 'annonces trouvées' : 'annonce trouvée' }}
                    </p>
                </div>

                {{-- Bouton pour publier une annonce --}}
                @auth
                    <a href="{{ route('annonces.create') }}" 
                       class="bg-gradient-accent hover:bg-gradient-secondary px-6 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-glow-accent inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Publier une annonce</span>
                    </a>
                @else
                    <div class="text-center">
                        <p class="text-white/60 mb-2">Envie de vendre ?</p>
                        <a href="{{ route('register') }}" 
                           class="bg-gradient-accent hover:bg-gradient-secondary px-6 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-glow-accent inline-flex items-center space-x-2">
                            <span>Créer un compte</span>
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Filtres actifs (tags) --}}
            @if(request()->hasAny(['search', 'category_id', 'localisation', 'prix_min', 'prix_max']))
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="text-sm text-white/60 mr-2">Filtres actifs :</span>
                        
                        @if(request('search'))
                            <span class="filter-tag glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center space-x-2">
                                <span>Recherche: "{{ request('search') }}"</span>
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white/60 hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </span>
                        @endif

                        @if(request('category_id'))
                            @php
                                $selectedCategory = $categories->find(request('category_id'));
                            @endphp
                            @if($selectedCategory)
                                <span class="filter-tag glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center space-x-2">
                                    <span>Catégorie: {{ $selectedCategory->nom }}</span>
                                    <a href="{{ request()->fullUrlWithQuery(['category_id' => null]) }}" class="text-white/60 hover:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        @endif

                        @if(request('localisation'))
                            <span class="filter-tag glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center space-x-2">
                                <span>Lieu: {{ request('localisation') }}</span>
                                <a href="{{ request()->fullUrlWithQuery(['localisation' => null]) }}" class="text-white/60 hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </span>
                        @endif

                        @if(request('prix_min') || request('prix_max'))
                            <span class="filter-tag glass-subtle px-3 py-1 rounded-full text-sm text-white flex items-center space-x-2">
                                <span>
                                    Prix: {{ request('prix_min', '0') }}€ - {{ request('prix_max', '∞') }}€
                                </span>
                                <a href="{{ request()->fullUrlWithQuery(['prix_min' => null, 'prix_max' => null]) }}" class="text-white/60 hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </span>
                        @endif

                        {{-- Bouton pour effacer tous les filtres --}}
                        <a href="{{ route('home') }}" 
                           class="filter-tag bg-red-500/20 border-red-500/30 px-3 py-1 rounded-full text-sm text-red-200 hover:bg-red-500/30 transition-colors">
                            Effacer tout
                        </a>
                    </div>
                </div>
            @endif

            {{-- Grille des annonces --}}
            @if($annonces->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($annonces as $annonce)
                        <div class="annonce-card rounded-2xl overflow-hidden animate-on-scroll">
                            
                            {{-- Image de l'annonce --}}
                            <div class="relative h-48 overflow-hidden">
                                @if($annonce->image)
                                    <img src="{{ Storage::url($annonce->image) }}" 
                                         alt="{{ $annonce->titre }}"
                                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                                         loading="lazy">
                                @else
                                    {{-- Image par défaut avec gradient --}}
                                    <div class="w-full h-full bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Badge de catégorie --}}
                                <div class="absolute top-3 left-3">
                                    <span class="glass-strong px-2 py-1 rounded-lg text-xs text-white font-medium">
                                        {{ $annonce->category->nom }}
                                    </span>
                                </div>

                                {{-- Bouton favori (pour utilisateurs connectés) --}}
                                @auth
                                    <button onclick="toggleFavorite({{ $annonce->id ;}})" 
                                            id="favorite-btn-{{ $annonce->id }}"
                                            class="absolute top-3 right-3 glass-strong p-2 rounded-lg hover:scale-110 transition-transform">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                @endauth
                            </div>

                            {{-- Contenu de la carte --}}
                            <div class="p-4">
                                
                                {{-- Titre et prix --}}
                                <div class="mb-3">
                                    <h3 class="text-lg font-semibold text-white mb-1 line-clamp-2">
                                        {{ $annonce->titre }}
                                    </h3>
                                    <div class="text-2xl font-bold text-primary-400">
                                        {{ number_format($annonce->prix, 0, ',', ' ') }} €
                                    </div>
                                </div>

                                {{-- Description courte --}}
                                <p class="text-white/70 text-sm mb-3 line-clamp-2">
                                    {{ Str::limit($annonce->description, 80) }}
                                </p>

                                {{-- Informations complémentaires --}}
                                <div class="flex items-center justify-between text-xs text-white/60 mb-4">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $annonce->localisation }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $annonce->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                {{-- Bouton voir détails --}}
                                <a href="{{ route('annonces.show', $annonce) }}" 
                                   class="block w-full bg-gradient-primary hover:bg-gradient-secondary text-center py-2 rounded-lg text-white font-medium transition-all duration-300 hover:scale-105">
                                    Voir les détails
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination avec glassmorphisme --}}
                <div class="flex justify-center">
                    <div class="glass-strong rounded-2xl p-4">
                        {{ $annonces->withQueryString()->links('pagination::tailwind') }}
                    </div>
                </div>

            @else
                {{-- Message si aucune annonce trouvée --}}
                <div class="text-center py-16">
                    <div class="glass-strong rounded-2xl p-8 max-w-md mx-auto">
                        <svg class="w-16 h-16 text-white/40 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-white mb-2">Aucune annonce trouvée</h3>
                        <p class="text-white/60 mb-4">
                            Essayez de modifier vos critères de recherche ou 
                            <a href="{{ route('home') }}" class="text-primary-400 hover:text-primary-300">voir toutes les annonces</a>.
                        </p>
                        @auth
                            <a href="{{ route('annonces.create') }}" 
                               class="bg-gradient-accent hover:bg-gradient-secondary px-6 py-2 rounded-lg text-white font-medium transition-all duration-300 hover:scale-105 inline-flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Publier la première annonce</span>
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Fonction pour gérer les favoris via AJAX
    @auth
    async function toggleFavorite(annonceId) {
        try {
            const response = await ajaxRequest(`/favoris/${annonceId}/toggle`, 'POST');
            const result = await response.json();
            
            if (response.ok) {
                // Mettre à jour l'icône du bouton favori
                const btn = document.getElementById(`favorite-btn-${annonceId}`);
                const icon = btn.querySelector('svg');
                
                if (result.is_favorite) {
                    // Annonce ajoutée aux favoris
                    icon.setAttribute('fill', 'currentColor');
                    icon.classList.add('text-red-500');
                    
                    // Animation de succès
                    btn.classList.add('animate-bounce');
                    setTimeout(() => btn.classList.remove('animate-bounce'), 600);
                    
                    // Message de succès (optionnel)
                    showToast('Ajouté aux favoris !', 'success');
                } else {
                    // Annonce retirée des favoris
                    icon.setAttribute('fill', 'none');
                    icon.classList.remove('text-red-500');
                    
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
    @endauth

    // Fonction pour afficher des messages toast
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-24 right-4 z-50 glass-strong rounded-xl p-4 text-white transform translate-x-full transition-transform duration-300`;
        
        // Couleur selon le type
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
        
        // Animation d'entrée
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        
        // Suppression automatique après 3 secondes
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Initialisation des favoris au chargement de la page
    @auth
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier l'état des favoris pour chaque annonce
        const favoriteButtons = document.querySelectorAll('[id^="favorite-btn-"]');
        favoriteButtons.forEach(async (btn) => {
            const annonceId = btn.id.split('-')[2];
            try {
                const response = await ajaxRequest(`/favoris/${annonceId}/check`);
                const result = await response.json();
                
                if (result.is_favorite) {
                    const icon = btn.querySelector('svg');
                    icon.setAttribute('fill', 'currentColor');
                    icon.classList.add('text-red-500');
                }
            } catch (error) {
                console.error('Erreur lors de la vérification des favoris:', error);
            }
        });
    });
    @endauth

    // Animation d'apparition des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer toutes les cartes d'annonces
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
</script>
@endpush