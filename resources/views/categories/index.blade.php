@extends('layouts.app')

@section('title','Liste des catégories')

@section('content')
<div class="pt-20 pb-8 px-4 sm:px-6 lg:px-8">
  <div class="max-w-7xl mx-auto space-y-8">

    {{-- Titre --}}
    <div class="text-center">
      <h1 class="text-4xl font-bold text-white mb-2">Toutes les catégories</h1>
      <p class="text-white/60">Parcourez nos {{$categories->count()}} catégories</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white/10 backdrop-blur border border-white/20 rounded-xl p-6 text-center">
        <div class="text-3xl font-bold text-white">{{$totalCategories}}</div>
        <div class="text-sm text-white/60">Catégories</div>
      </div>
      <div class="bg-white/10 backdrop-blur border border-white/20 rounded-xl p-6 text-center">
        <div class="text-3xl font-bold text-white">{{$totalAnnonces}}</div>
        <div class="text-sm text-white/60">Annonces</div>
      </div>
      <div class="bg-white/10 backdrop-blur border border-white/20 rounded-xl p-6 text-center">
        <div class="text-2xl font-bold text-white">{{$mostPopularCategory->name ?? 'Aucune'}}</div>
        <div class="text-sm text-white/60">La + populaire</div>
      </div>
    </div>

    {{-- Grille --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      @foreach($categories as $c)
        <a href="{{route('categories.show',$c)}}" class="group bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition">
          <h2 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-400">{{$c->name}}</h2>
          <p class="text-white/60 text-sm mb-3 line-clamp-2">{{$c->description ?? 'Pas de description'}}</p>
          <div class="flex justify-between items-center">
            <span class="text-sm text-white/80">{{$c->annonces_count}} annonce(s)</span>
            <span class="text-indigo-400 text-sm">→</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/script_index_categories.js'])
@endpush