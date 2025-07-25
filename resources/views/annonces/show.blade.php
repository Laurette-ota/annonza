@extends('layouts.app')

@section('title', $annonce->titre.' - '.$annonce->price_display)
@section('description', Str::limit($annonce->description,160))

@section('content')
<div class="pt-20 pb-8">

  {{-- Fil d'Ariane --}}
  <nav class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white/10 backdrop-blur border border-white/20 rounded-xl px-4 py-2">
        <ol class="flex items-center space-x-2 text-sm text-white/70">
          <li><a href="{{route('home')}}" class="hover:text-white">Accueil</a></li>
          <li><a href="{{route('categories.show',$annonce->category)}}" class="hover:text-white">{{$annonce->category->name}}</a></li>
          <li class="text-white font-medium">{{Str::limit($annonce->title,30)}}</li>
        </ol>
      </div>
    </div>
  </nav>

  <div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

      {{-- Colonne principale --}}
      <div class="lg:col-span-2 space-y-6">
        {{-- Image --}}
        <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl overflow-hidden">
          @if($annonce->image)
            <img src="{{Storage::url($annonce->image)}}" alt="{{$annonce->title}}" class="w-full h-96 object-cover cursor-pointer" onclick="openImageModal('{{Storage::url($annonce->image)}}')">
          @else
            <div class="w-full h-96 bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
              <svg class="w-24 h-24 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          @endif
          <a href="{{route('categories.show',$annonce->category)}}" class="absolute top-4 left-4 bg-white/20 backdrop-blur px-3 py-1 rounded-xl text-white font-medium hover:bg-white/30 transition">
            {{$annonce->category->name}}
          </a>
          @auth
            <button id="favBtn" onclick="toggleFavorite({{$annonce->id}})" class="absolute top-4 right-4 bg-white/20 backdrop-blur p-2 rounded-xl text-white hover:scale-110 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          @endauth
        </div>

        {{-- Infos --}}
        <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
          <h1 class="text-3xl font-bold text-white mb-2">{{$annonce->title}}</h1>
          <div class="text-4xl font-bold text-indigo-400 mb-4">
            {{number_format($annonce->price,0,',',' ')}} {{$annonce->currency->symbol}}
          </div>
          <div class="flex flex-wrap gap-3 mb-4">
            <span class="bg-white/10 px-3 py-1 rounded-lg text-white text-sm flex items-center">
              <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>{{$annonce->location}}
            </span>
            <span class="bg-white/10 px-3 py-1 rounded-lg text-white text-sm flex items-center">
              <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>{{$annonce->user->name}}
            </span>
            <span class="bg-white/10 px-3 py-1 rounded-lg text-white text-sm flex items-center">
              <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{$annonce->created_at->diffForHumans()}}
            </span>
          </div>
          <h2 class="text-xl font-semibold text-white mb-2">Description</h2>
          <p class="text-white/80 whitespace-pre-line">{{$annonce->description}}</p>
        </div>

        {{-- Similaires --}}
        @if($annoncesSimilaires->count())
          <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-white mb-4">Annonces similaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              @foreach($annoncesSimilaires as $sim)
                <a href="{{route('annonces.show',$sim)}}" class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl p-4 transition">
                  <div class="flex space-x-4">
                    @if($sim->image)
                      <img src="{{Storage::url($sim->image)}}" class="w-16 h-16 rounded-lg object-cover">
                    @else
                      <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg"></div>
                    @endif
                    <div>
                      <h3 class="text-white font-medium truncate">{{$sim->title}}</h3>
                      <p class="text-white/60 text-sm">{{$sim->location}}</p>
                      <p class="text-indigo-400 font-semibold">{{number_format($sim->price,0,',',' ')}}{{$sim->currency->symbol}}</p>
                    </div>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      {{-- Sidebar --}}
      <div class="space-y-6">
        <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6 sticky top-24">
          <div class="text-center mb-4">
            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
              <span class="text-2xl font-bold text-white">{{substr($annonce->user->name,0,1)}}</span>
            </div>
            <h3 class="text-lg font-semibold text-white">{{$annonce->user->name}}</h3>
            <p class="text-white/60 text-sm">Membre depuis {{$annonce->user->created_at->format('M Y')}}</p>
          </div>

          <div class="space-y-3">
            @auth
              @if(auth()->id()!==$annonce->user_id)
                <button onclick="showContactModal()" class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold py-3 rounded-xl transition">Contacter le vendeur</button>
              @else
                <a href="{{route('annonces.edit',$annonce)}}" class="w-full bg-gradient-to-r from-pink-500 to-rose-500 text-white font-semibold py-3 rounded-xl transition text-center block">Modifier l’annonce</a>
                <button onclick="confirmDelete()" class="w-full bg-red-500/20 border border-red-500/30 text-red-300 font-semibold py-3 rounded-xl transition">Supprimer</button>
              @endif
            @else
              <a href="{{route('login')}}" class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold py-3 rounded-xl transition text-center block">Se connecter</a>
            @endauth
            <button onclick="shareAnnonce()" class="w-full bg-white/5 border border-white/10 text-white font-medium py-3 rounded-xl transition">Partager</button>
          </div>

          <div class="mt-4 pt-4 border-t border-white/20 text-white/60 text-sm space-y-2">
            <div class="flex justify-between"><span>Référence</span><span class="text-white">#{{$annonce->id}}</span></div>
            <div class="flex justify-between"><span>Publié</span><span class="text-white">{{$annonce->created_at->format('d/m/Y')}}</span></div>
            @if($annonce->updated_at->ne($annonce->created_at))
              <div class="flex justify-between"><span>Modifié</span><span class="text-white">{{$annonce->updated_at->format('d/m/Y')}}</span></div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modals --}}
  <div id="imageModal" class="fixed inset-0 z-50 bg-black/80 hidden items-center justify-center p-4" onclick="closeImageModal()">
    <img id="modalImg" src="" class="max-w-full max-h-full rounded-xl">
    <button onclick="closeImageModal()" class="absolute top-4 right-4 bg-white/10 backdrop-blur p-2 rounded-full text-white">✕</button>
  </div>

  <div id="contactModal" class="fixed inset-0 z-50 bg-black/80 hidden items-center justify-center p-4">
    <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6 max-w-sm w-full">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-white font-semibold">Contacter {{$annonce->user->name}}</h3>
        <button onclick="closeContactModal()" class="text-white/60">✕</button>
      </div>
      <p class="text-white/80 mb-4">Email&nbsp;: {{$annonce->user->email}}</p>
      <div class="flex space-x-3">
        <button onclick="closeContactModal()" class="flex-1 bg-white/5 border border-white/10 text-white py-2 rounded-lg">Fermer</button>
        <a href="mailto:{{$annonce->user->email}}" class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-2 rounded-lg text-center">E-mail</a>
      </div>
    </div>
  </div>

  <form id="deleteForm" action="{{route('annonces.destroy',$annonce)}}" method="POST" class="hidden">
    @csrf @method('DELETE')
  </form>
</div>
@endsection

@push('scripts')
@vite(['resources/js/script_show_annonces.js'])
@endpush