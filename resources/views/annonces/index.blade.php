{{--
Vue d'accueil - Liste des annonces avec recherche et filtres
Design 100 % Tailwind CSS + glassmorphisme
--}}

@extends('layouts.app')

@section('title', 'Accueil - Découvrez nos annonces')
@section('description', 'Parcourez notre sélection d\'annonces variées. Trouvez ce que vous cherchez grâce à notre système de recherche et de filtrage avancé.')
@section('keywords', 'annonces, recherche, catégories, vente, achat, services')

@section('content')
  <div class="pt-20 pb-8">

    {{-- Section héro --}}
    <section class="relative py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto text-center">
      <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
      <span
        class="bg-gradient-to-r from-purple-400 via-pink-400 to-indigo-400 bg-clip-text text-transparent">Découvrez</span><br>
      <span class="text-white">nos annonces</span>
      </h1>
      <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
      Trouvez exactement ce que vous cherchez parmi des milliers d'annonces. Achetez, vendez, échangez en toute
      simplicité.
      </p>

      {{-- Barre de recherche --}}
      <form action="{{ route('home') }}" method="GET"
      class="max-w-4xl mx-auto bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
        <label for="search" class="block text-sm font-medium text-white mb-2">Que recherchez-vous ?</label>
        <input type="text" id="search" name="search" value="{{ request('search') }}"
          placeholder="Ex: iPhone, Voiture..."
          class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
        <label for="category" class="block text-sm font-medium text-white mb-2">Catégorie</label>
        <select id="category" name="category_id"
          class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <option value="" class="bg-gray-800 text-white">Toutes</option>
          @foreach($categories as $category)
        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}
        class="bg-gray-800 text-white">
        {{ $category->name }}
        </option>
      @endforeach
        </select>
        </div>

        <div class="flex items-end">
        <button type="submit"
          class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 px-6 py-3 rounded-xl text-white font-medium transition transform hover:scale-105">
          Rechercher
        </button>
        </div>
      </div>

      {{-- Filtres avancés --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-white/20">
        <input type="text" name="localisation" placeholder="Lieu" value="{{ request('localisation') }}"
        class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <input type="number" name="prix_min" placeholder="Prix min" min="0" value="{{ request('prix_min') }}"
        class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <input type="number" name="prix_max" placeholder="Prix max" min="0" value="{{ request('prix_max') }}"
        class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>
      </form>

      {{-- Statistiques --}}
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mt-8">
      @foreach([['total_annonces', 'Annonces'], [$categories->count(), 'Catégories'], ['total_utilisateurs', 'Utilisateurs'], ['annonces_aujourdhui', "Aujourd'hui"]] as [$stat, $label])
      <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl p-4 text-center">
      <div class="text-2xl font-bold text-white">{{ $stats[$loop->index === 1 ? 'count' : $stat] ?? $stat }}</div>
      <div class="text-sm text-white/60">{{ $label }}</div>
      </div>
    @endforeach
      </div>
    </div>
    </section>

    {{-- Résultats --}}
    <section class="px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
      <div>
        <h2 class="text-2xl font-bold text-white mb-2">
        {{ request()->hasAny(['search', 'category_id', 'localisation', 'prix_min', 'prix_max']) ? 'Résultats de recherche' : 'Toutes les annonces' }}
        </h2>
        <p class="text-white/60">{{ $annonces->total() }} {{ $annonces->total() > 1 ? 'annonces' : 'annonce' }}</p>
      </div>

      @auth
      <a href="{{ route('annonces.create') }}"
      class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 px-6 py-3 rounded-xl text-white font-medium transition transform hover:scale-105">
      Publier une annonce
      </a>
    @else
      <a href="{{ route('register') }}"
      class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 px-6 py-3 rounded-xl text-white font-medium transition transform hover:scale-105">
      Créer un compte
      </a>
    @endauth
      </div>

      {{-- Filtres actifs --}}
      <div class="mb-6 flex flex-wrap gap-2 text-sm">
      @foreach(['search', 'category_id', 'localisation', 'prix_min', 'prix_max'] as $key)
      @if(request($key))
      <span
      class="bg-white/10 backdrop-blur border border-white/20 px-3 py-1 rounded-full text-white flex items-center space-x-2">
      <span>{{ ucfirst($key) }}: {{ request($key) }}</span>
      <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" class="text-white/60 hover:text-white">&times;</a>
      </span>
      @endif
    @endforeach
      @if(request()->hasAny(['search', 'category_id', 'localisation', 'prix_min', 'prix_max']))
      <a href="{{ route('home') }}"
      class="bg-red-500/20 border border-red-500/30 px-3 py-1 rounded-full text-red-200 hover:bg-red-500/30">Effacer
      tout</a>
    @endif
      </div>

      {{-- Grille d'annonces --}}
      @if($annonces->count())
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      @foreach($annonces as $annonce)
      <div
      class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-indigo-500/30">
      <div class="relative h-48">
      @if($annonce->image)
      <img src="{{ Storage::url($annonce->image) }}" alt="{{ $annonce->titre }}"
      class="w-full h-full object-cover">
      @else
      <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
      <svg class="w-12 h-12 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      </div>
      @endif
      <div
      class="absolute top-3 left-3 bg-white/20 backdrop-blur border border-white/30 px-2 py-1 rounded text-xs text-white font-medium">
      {{ $annonce->category->name ?? 'Sans catégorie' }}
      </div>
      @auth
      <button onclick="toggleFavorite({{ $annonce->id }})" id="favorite-btn-{{ $annonce->id }}"
      class="absolute top-3 right-3 bg-white/20 backdrop-blur border border-white/30 p-2 rounded-lg hover:scale-110 transition">
      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
      </svg>
      </button>
      @endauth
      </div>

      <div class="p-4">
      <h3 class="text-lg font-semibold text-white mb-1 truncate">{{ $annonce->titre }}</h3>
      <div class="text-xl font-bold text-indigo-400 mb-2">{{ number_format($annonce->prix, 0, ',', ' ') }} €</div>
      <p class="text-white/70 text-sm mb-3 h-10 overflow-hidden">{{ Str::limit($annonce->description, 70) }}</p>
      <div class="flex justify-between text-xs text-white/60 mb-4">
      <span>{{ $annonce->localisation }}</span>
      <span>{{ $annonce->created_at->diffForHumans() }}</span>
      </div>
      <a href="{{ route('annonces.show', $annonce) }}"
      class="block w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-center py-2 rounded-lg text-white font-medium transition transform hover:scale-105">
      Voir les détails
      </a>
      </div>
      </div>
    @endforeach
      </div>

      <div class="mt-8 flex justify-center">
      <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-4">
      {{ $annonces->withQueryString()->links() }}
      </div>
      </div>
    @else
      <div class="text-center py-16">
      <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 max-w-md mx-auto">
      <svg class="w-12 h-12 text-white/40 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <h3 class="text-xl font-semibold text-white mb-2">Aucune annonce</h3>
      <p class="text-white/60 mb-4">
      Aucune annonce trouvée. <a href="{{ route('home') }}" class="text-indigo-400">Voir toutes</a>
      </p>
      @auth
      <a href="{{ route('annonces.create') }}"
      class="bg-gradient-to-r from-pink-500 to-rose-500 px-4 py-2 rounded-lg text-white">Publier la première</a>
      @endauth
      </div>
      </div>
    @endif
    </div>
    </section>
  </div>
@endsection

@push('scripts')
  @vite(['resources/js/annonces/index.js'])
@endpush