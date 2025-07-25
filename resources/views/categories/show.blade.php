{{--
  Vue d'affichage des annonces d'une catégorie
  100 % Tailwind CSS + glassmorphisme + devise
--}}

@extends('layouts.app')

@section('title', 'Annonces dans ' . $category->name . ' - ' . $annonces->total() . ' résultats')
@section('description', 'Découvrez toutes les annonces dans la catégorie ' . $category->name . '. ' . $annonces->total() . ' annonces disponibles.')
@section('keywords', $category->name . ', annonces, ' . $category->description . ', marketplace')

@section('content')
<div class="pt-20 pb-8">

  {{-- Fil d’Ariane --}}
  <nav class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-xl px-4 py-2">
        <ol class="flex items-center space-x-2 text-sm text-white/70">
          <li>
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Accueil</a>
          </li>
          <li class="flex items-center">
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white font-medium">{{ $category->name }}</span>
          </li>
        </ol>
      </div>
    </div>
  </nav>

  {{-- En-tête de la catégorie --}}
  <div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-7xl mx-auto text-center">
      <div class="bg-gradient-to-r from-indigo-500/20 to-purple-500/20 backdrop-blur-lg border border-white/20 inline-flex items-center px-4 py-2 rounded-full text-sm text-white mb-4">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        {{ $category->name }}
      </div>
      <h1 class="text-4xl font-bold text-white mb-4">
        <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">{{ $annonces->total() }} {{ Str::plural('annonce', $annonces->total()) }}</span>
      </h1>
      @if($category->description)
        <p class="text-xl text-white/80 max-w-3xl mx-auto">{{ $category->description }}</p>
      @endif
    </div>
  </div>

  {{-- Statistiques --}}
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 text-center">
          <div class="text-3xl font-bold text-indigo-400">{{ $annonces->total() }}</div>
          <div class="text-sm text-white/60">{{ Str::plural('Annonce', $annonces->total()) }}</div>
        </div>

        <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 text-center">
          <div class="text-3xl font-bold text-green-400">
            {{ $stats['prix_min'] ? number_format($stats['prix_min'], 0, ',', ' ') : 'N/A' }}
          </div>
          <div class="text-sm text-white/60">Prix min</div>
        </div>

        <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 text-center">
          <div class="text-3xl font-bold text-purple-400">
            {{ $stats['prix_max'] ? number_format($stats['prix_max'], 0, ',', ' ') : 'N/A' }}
          </div>
          <div class="text-sm text-white/60">Prix max</div>
        </div>

        <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 text-center">
          <div class="text-3xl font-bold text-pink-400">
            {{ $stats['prix_moyen'] ? number_format($stats['prix_moyen'], 0, ',', ' ') : 'N/A' }}
          </div>
          <div class="text-sm text-white/60">Prix moyen</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filtres --}}
  <div class="px-4 sm:px-6 lg:px-8 mb-8">
    <div class="max-w-7xl mx-auto">
      <form method="GET" action="{{ route('categories.show', $category) }}" id="filterForm" class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
          <!-- Recherche -->
          <div class="lg:col-span-2">
            <label for="search" class="block text-sm font-medium text-white mb-2">Rechercher</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Mot-clé..." class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-indigo-500">
          </div>

          <!-- Localisation -->
          <div>
            <label for="localisation" class="block text-sm font-medium text-white mb-2">Localisation</label>
            <input type="text" name="localisation" value="{{ request('localisation') }}" placeholder="Ville..." class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60">
          </div>

          <!-- Prix min -->
          <div>
            <label for="prix_min" class="block text-sm font-medium text-white mb-2">Prix min</label>
            <input type="number" name="prix_min" value="{{ request('prix_min') }}" placeholder="0" min="0" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
          </div>

          <!-- Prix max -->
          <div>
            <label for="prix_max" class="block text-sm font-medium text-white mb-2">Prix max</label>
            <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="∞" min="0" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
          </div>

          <!-- Devise -->
          <div>
            <label for="currency_id" class="block text-sm font-medium text-white mb-2">Devise</label>
            <select name="currency_id" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
              <option value="">Toutes</option>
              @foreach(\App\Models\Currency::orderBy('code')->get() as $cur)
                <option value="{{ $cur->id }}" {{ request('currency_id') == $cur->id ? 'selected' : '' }} class="bg-gray-800 text-white">
                  {{ $cur->code }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Tri -->
          <div>
            <label for="sort" class="block text-sm font-medium text-white mb-2">Trier par</label>
            <select name="sort" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white">
              <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }} class="bg-gray-800">Plus récent</option>
              <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }} class="bg-gray-800">Prix ↗</option>
              <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }} class="bg-gray-800">Prix ↘</option>
              <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }} class="bg-gray-800">A-Z</option>
            </select>
          </div>

          <!-- Boutons -->
          <div class="flex items-end space-x-2">
            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-medium rounded-xl transition">Filtrer</button>
            <a href="{{ route('categories.show', $category) }}" class="w-full px-4 py-3 bg-white/10 border border-white/20 text-white font-medium rounded-xl hover:bg-white/20 transition text-center">Réinitialiser</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Résultats --}}
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      @if($annonces->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          @foreach($annonces as $annonce)
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-indigo-500/30">
              <div class="relative h-48">
                @if($annonce->image)
                  <img src="{{ Storage::url($annonce->image) }}" alt="{{ $annonce->title }}" class="w-full h-full object-cover">
                @else
                  <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                    <svg class="w-12 h-12 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                @endif
                <div class="absolute top-3 left-3 bg-white/20 backdrop-blur px-2 py-1 rounded text-xs text-white font-medium">{{ $annonce->category->name }}</div>
                @auth
                  <button onclick="toggleFavorite({{ $annonce->id }})" id="favorite-btn-{{ $annonce->id }}" class="absolute top-3 right-3 bg-white/20 backdrop-blur p-2 rounded-lg hover:scale-110 transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                  </button>
                @endauth
              </div>

              <div class="p-4">
                <h3 class="text-lg font-semibold text-white truncate">{{ $annonce->title }}</h3>
                <div class="text-xl font-bold text-indigo-400">
                  {{ number_format($annonce->price, 0, ',', ' ') }} {{ $annonce->currency->symbol }}
                </div>
                <p class="text-white/70 text-sm mt-1 line-clamp-2">{{ Str::limit($annonce->description, 70) }}</p>
                <div class="flex justify-between text-xs text-white/60 mt-3">
                  <span>{{ $annonce->location }}</span>
                  <span>{{ $annonce->created_at->diffForHumans() }}</span>
                </div>
                <a href="{{ route('annonces.show', $annonce) }}" class="block w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-center py-2 rounded-lg text-white font-medium mt-4 transition">Voir</a>
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-xl font-semibold text-white mb-2">Aucune annonce trouvée</h3>
            <p class="text-white/60 mb-4">
              Aucune annonce ne correspond à vos critères dans la catégorie "{{ $category->name }}".
            </p>
            <a href="{{ route('categories.show', $category) }}" class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 px-4 py-2 rounded-lg text-white">Voir toutes les annonces</a>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/categories/show.js'])
@endpush