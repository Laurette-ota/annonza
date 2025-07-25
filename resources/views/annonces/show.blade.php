{{--
  Vue détaillée d'une annonce
  Cette vue affiche toutes les informations d'une annonce avec interactions
  Design avec glassmorphisme et responsive
--}}

@extends('layouts.app')

@section('title', $annonce->titre . ' - ' . number_format($annonce->prix, 0, ',', ' ') . '€')
@section('description', Str::limit($annonce->description, 160))
@section('keywords', $annonce->titre . ', ' . $annonce->category->nom . ', ' . $annonce->localisation . ', annonce, vente')

@section('og_title', $annonce->titre)
@section('og_description', Str::limit($annonce->description, 160))

@push('styles')
<style>
    /* Styles pour la galerie d'images */
    .image-gallery {
        position: relative;
    }
    
    .main-image {
        transition: all 0.3s ease;
        cursor: zoom-in;
    }
    
    .main-image:hover {
        transform: scale(1.02);
    }
    
    /* Modal pour l'image en plein écran */
    .image-modal {
        backdrop-filter: blur(20px);
        background: rgba(0, 0, 0, 0.9);
    }
    
    /* Animation pour les boutons d'action */
    .action-button {
        transition: all 0.3s ease;
        backdrop-filter: blur(15px);
    }
    
    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    
    /* Styles pour les informations de contact */
    .contact-info {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    /* Animation pour les tags */
    .info-tag {
        transition: all 0.2s ease;
    }
    
    .info-tag:hover {
        transform: scale(1.05);
        background: rgba(255, 255, 255, 0.15);
    }
</style>
@endpush

@section('content')
<div class="pt-20 pb-8"> {{-- Padding pour compenser la navigation fixe --}}
    
    {{-- Fil d'Ariane --}}
    <nav class="px-4 sm:px-6 lg:px-8 py-4">
        <div class="max-w-7xl mx-auto">
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
                        <a href="{{ route('categories.show', $annonce->category) }}" class="hover:text-white transition-colors">
                            {{ $annonce->category->nom }}
                        </a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-white font-medium">{{ Str::limit($annonce->titre, 30) }}</span>
                    </li>
                </ol>
            </div>
        </div>
    </nav>

    {{-- Contenu principal --}}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Colonne principale (image et description) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Galerie d'images --}}
                    <div class="glass-strong rounded-2xl overflow-hidden">
                        <div class="image-gallery">
                            @if($annonce->image)
                                <img src="{{ Storage::url($annonce->image) }}" 
                                     alt="{{ $annonce->titre }}"
                                     class="main-image w-full h-96 object-cover"
                                     onclick="openImageModal(this.src)">
                            @else
                                {{-- Image par défaut avec gradient et icône --}}
                                <div class="w-full h-96 bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-24 h-24 text-white/60 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-white/60 text-lg">Aucune image disponible</p>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Badge de catégorie sur l'image --}}
                            <div class="absolute top-4 left-4">
                                <a href="{{ route('categories.show', $annonce->category) }}" 
                                   class="glass-strong px-3 py-2 rounded-xl text-white font-medium hover:bg-white/20 transition-colors">
                                    {{ $annonce->category->nom }}
                                </a>
                            </div>
                            
                            {{-- Bouton favori --}}
                            @auth
                                <button onclick="toggleFavorite({{ $annonce->id }})" 
                                        id="favorite-btn-{{ $annonce->id }}"
                                        class="absolute top-4 right-4 glass-strong p-3 rounded-xl hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            @endauth
                        </div>
                    </div>

                    {{-- Informations principales --}}
                    <div class="glass-strong rounded-2xl p-6">
                        
                        {{-- Titre et prix --}}
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-white mb-3">{{ $annonce->titre }}</h1>
                            <div class="flex items-center justify-between">
                                <div class="text-4xl font-bold text-primary-400">
                                    {{ number_format($annonce->prix, 0, ',', ' ') }} €
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-white/60">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $annonce->created_at->format('d/m/Y à H:i') }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span id="view-count">{{ $annonce->vues ?? 0 }} vues</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tags d'informations --}}
                        <div class="flex flex-wrap gap-3 mb-6">
                            <div class="info-tag glass-subtle px-3 py-2 rounded-lg flex items-center space-x-2">
                                <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-white">{{ $annonce->localisation }}</span>
                            </div>
                            
                            <div class="info-tag glass-subtle px-3 py-2 rounded-lg flex items-center space-x-2">
                                <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-white">{{ $annonce->user->name }}</span>
                            </div>
                            
                            <div class="info-tag glass-subtle px-3 py-2 rounded-lg flex items-center space-x-2">
                                <svg class="w-4 h-4 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="text-white">{{ $annonce->category->nom }}</span>
                            </div>
                            
                            <div class="info-tag glass-subtle px-3 py-2 rounded-lg flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-white">Disponible</span>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-4">Description</h2>
                            <div class="prose prose-invert max-w-none">
                                <p class="text-white/80 leading-relaxed whitespace-pre-line">{{ $annonce->description }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Annonces similaires --}}
                    @if($annoncesSimilaires->count() > 0)
                        <div class="glass-strong rounded-2xl p-6">
                            <h2 class="text-xl font-semibold text-white mb-4">Annonces similaires</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($annoncesSimilaires as $similaire)
                                    <a href="{{ route('annonces.show', $similaire) }}" 
                                       class="glass-subtle rounded-xl p-4 hover:bg-white/15 transition-colors group">
                                        <div class="flex space-x-4">
                                            @if($similaire->image)
                                                <img src="{{ Storage::url($similaire->image) }}" 
                                                     alt="{{ $similaire->titre }}"
                                                     class="w-16 h-16 object-cover rounded-lg">
                                            @else
                                                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h3 class="text-white font-medium group-hover:text-primary-400 transition-colors line-clamp-1">
                                                    {{ $similaire->titre }}
                                                </h3>
                                                <p class="text-white/60 text-sm line-clamp-1">{{ $similaire->localisation }}</p>
                                                <p class="text-primary-400 font-semibold">
                                                    {{ number_format($similaire->prix, 0, ',', ' ') }} €
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Colonne latérale (contact et actions) --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Informations de contact --}}
                    <div class="contact-info rounded-2xl p-6 sticky top-24">
                        
                        {{-- Profil du vendeur --}}
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-gradient-primary rounded-full flex items-center justify-center mx-auto mb-3">
                                <span class="text-2xl font-bold text-white">
                                    {{ substr($annonce->user->name, 0, 1) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-white">{{ $annonce->user->name }}</h3>
                            <p class="text-white/60 text-sm">
                                Membre depuis {{ $annonce->user->created_at->format('M Y') }}
                            </p>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="space-y-3">
                            
                            {{-- Bouton de contact principal --}}
                            @auth
                                @if(auth()->id() !== $annonce->user_id)
                                    <button onclick="showContactModal()" 
                                            class="action-button w-full bg-gradient-primary hover:bg-gradient-secondary text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300">
                                        <div class="flex items-center justify-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Contacter le vendeur</span>
                                        </div>
                                    </button>
                                @else
                                    {{-- Actions pour le propriétaire --}}
                                    <div class="space-y-2">
                                        <a href="{{ route('annonces.edit', $annonce) }}" 
                                           class="action-button w-full bg-gradient-accent hover:bg-gradient-secondary text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 block text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span>Modifier l'annonce</span>
                                            </div>
                                        </a>
                                        
                                        <button onclick="confirmDelete()" 
                                                class="action-button w-full bg-red-500/20 border border-red-500/30 hover:bg-red-500/30 text-red-200 font-semibold py-3 px-4 rounded-xl transition-all duration-300">
                                            <div class="flex items-center justify-center space-x-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span>Supprimer l'annonce</span>
                                            </div>
                                        </button>
                                    </div>
                                @endif
                            @else
                                {{-- Invitation à se connecter --}}
                                <div class="text-center">
                                    <p class="text-white/60 mb-4">Connectez-vous pour contacter le vendeur</p>
                                    <a href="{{ route('login') }}" 
                                       class="action-button w-full bg-gradient-primary hover:bg-gradient-secondary text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 block text-center">
                                        Se connecter
                                    </a>
                                </div>
                            @endauth

                            {{-- Bouton de partage --}}
                            <button onclick="shareAnnonce()" 
                                    class="action-button w-full glass-subtle border border-white/20 hover:bg-white/15 text-white font-medium py-3 px-4 rounded-xl transition-all duration-300">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                    <span>Partager</span>
                                </div>
                            </button>

                            {{-- Bouton signaler --}}
                            @auth
                                @if(auth()->id() !== $annonce->user_id)
                                    <button onclick="reportAnnonce()" 
                                            class="w-full text-white/60 hover:text-red-400 text-sm py-2 transition-colors">
                                        <div class="flex items-center justify-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                            <span>Signaler cette annonce</span>
                                        </div>
                                    </button>
                                @endif
                            @endauth
                        </div>

                        {{-- Informations supplémentaires --}}
                        <div class="mt-6 pt-6 border-t border-white/20">
                            <div class="space-y-3 text-sm text-white/60">
                                <div class="flex items-center justify-between">
                                    <span>Référence :</span>
                                    <span class="text-white">#{{ $annonce->id }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Publié le :</span>
                                    <span class="text-white">{{ $annonce->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($annonce->updated_at != $annonce->created_at)
                                    <div class="flex items-center justify-between">
                                        <span>Modifié le :</span>
                                        <span class="text-white">{{ $annonce->updated_at->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal pour l'image en plein écran --}}
    <div id="imageModal" class="fixed inset-0 z-50 hidden image-modal" onclick="closeImageModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <img id="modalImage" src="" alt="Image en plein écran" class="max-w-full max-h-full object-contain rounded-xl">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 glass-strong p-2 rounded-lg text-white hover:bg-white/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Modal de contact --}}
    @auth
        @if(auth()->id() !== $annonce->user_id)
            <div id="contactModal" class="fixed inset-0 z-50 hidden" style="backdrop-filter: blur(20px); background: rgba(0, 0, 0, 0.8);">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="glass-strong rounded-2xl p-6 max-w-md w-full">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-white">Contacter {{ $annonce->user->name }}</h3>
                            <button onclick="closeContactModal()" class="text-white/60 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <p class="text-white/80">
                                Intéressé(e) par "{{ $annonce->titre }}" ?
                            </p>
                            
                            {{-- Informations de contact simulées (à remplacer par un vrai système de messagerie) --}}
                            <div class="glass-subtle rounded-xl p-4">
                                <p class="text-white/60 text-sm mb-2">Informations de contact :</p>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-white">{{ $annonce->user->email }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span class="text-white">+33 6 XX XX XX XX</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button onclick="closeContactModal()" 
                                        class="flex-1 glass-subtle border border-white/20 text-white py-2 px-4 rounded-lg hover:bg-white/15 transition-colors">
                                    Fermer
                                </button>
                                <a href="mailto:{{ $annonce->user->email }}?subject=Intéressé par {{ $annonce->titre }}" 
                                   class="flex-1 bg-gradient-primary hover:bg-gradient-secondary text-white py-2 px-4 rounded-lg text-center transition-colors">
                                    Envoyer un email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    {{-- Formulaire de suppression (caché) --}}
    @auth
        @if(auth()->id() === $annonce->user_id)
            <form id="deleteForm" action="{{ route('annonces.destroy', $annonce) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    @endauth
</div>
@endsection

@push('scripts')
<script>
    // Fonction pour gérer les favoris
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
                    icon.classList.add('text-red-500');
                    btn.classList.add('animate-bounce');
                    setTimeout(() => btn.classList.remove('animate-bounce'), 600);
                    showToast('Ajouté aux favoris !', 'success');
                } else {
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

    // Vérifier l'état du favori au chargement
    document.addEventListener('DOMContentLoaded', async function() {
        const favoriteBtn = document.getElementById('favorite-btn-{{ $annonce->id }}');
        if (favoriteBtn) {
            try {
                const response = await ajaxRequest('/favoris/{{ $annonce->id }}/check');
                const result = await response.json();
                
                if (result.is_favorite) {
                    const icon = favoriteBtn.querySelector('svg');
                    icon.setAttribute('fill', 'currentColor');
                    icon.classList.add('text-red-500');
                }
            } catch (error) {
                console.error('Erreur lors de la vérification du favori:', error);
            }
        }
    });
    @endauth

    // Gestion de la modal d'image
    function openImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Gestion de la modal de contact
    @auth
    @if(auth()->id() !== $annonce->user_id)
    function showContactModal() {
        const modal = document.getElementById('contactModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeContactModal() {
        const modal = document.getElementById('contactModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    @endif
    @endauth

    // Fonction de partage
    function shareAnnonce() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $annonce->titre }}',
                text: '{{ Str::limit($annonce->description, 100) }}',
                url: window.location.href
            });
        } else {
            // Fallback : copier l'URL
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('Lien copié dans le presse-papiers !', 'success');
            });
        }
    }

    // Fonction de signalement
    @auth
    @if(auth()->id() !== $annonce->user_id)
    function reportAnnonce() {
        if (confirm('Voulez-vous vraiment signaler cette annonce ?')) {
            // Ici, vous pourriez implémenter un système de signalement
            showToast('Annonce signalée. Merci pour votre vigilance.', 'info');
        }
    }
    @endif
    @endauth

    // Confirmation de suppression
    @auth
    @if(auth()->id() === $annonce->user_id)
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.')) {
            document.getElementById('deleteForm').submit();
        }
    }
    @endif
    @endauth

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
        }, 3000);
    }

    // Fermer les modals avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
            @auth
            @if(auth()->id() !== $annonce->user_id)
            closeContactModal();
            @endif
            @endauth
        }
    });

    // Incrémenter le compteur de vues (simulé)
    document.addEventListener('DOMContentLoaded', function() {
        // Simuler l'incrémentation des vues après 3 secondes
        setTimeout(() => {
            const viewCount = document.getElementById('view-count');
            if (viewCount) {
                const currentViews = parseInt(viewCount.textContent);
                viewCount.textContent = `${currentViews + 1} vues`;
            }
        }, 3000);
    });
</script>
@endpush
