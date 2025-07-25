@extends('layouts.app')

@section('title', 'Liste des catégories')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Liste des catégories</h1>
    
    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold">Total catégories</h3>
            <p class="text-2xl">{{ $totalCategories }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold">Total annonces</h3>
            <p class="text-2xl">{{ $totalAnnonces }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold">Catégorie la plus populaire</h3>
            <p class="text-xl">
                {{ $mostPopularCategory->name ?? 'Aucune' }} 
                @if($mostPopularCategory)
                    ({{ $mostPopularCategory->annonces_count }} annonces)
                @endif
            </p>
        </div>
    </div>

    <!-- Liste des catégories -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow">
                <a href="{{ route('categories.show', $category) }}" class="block">
                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2">{{ $category->name }}</h2>
                        <p class="text-gray-600 mb-3">{{ $category->description ?? 'Pas de description' }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">
                                {{ $category->annonces_count }} annonce(s)
                            </span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                Voir
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection