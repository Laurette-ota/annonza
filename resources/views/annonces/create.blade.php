@extends('layouts.app')

@section('title','Publier une annonce')
@section('description','Déposez votre annonce gratuitement sur notre plateforme.')

@section('content')
<div class="pt-20 pb-8 px-4 sm:px-6 lg:px-8">
  <div class="max-w-4xl mx-auto">

    {{-- Erreurs --}}
    @if($errors->any())
      <div class="bg-red-500/10 border border-red-500/30 rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-semibold text-red-300 mb-2">Erreurs de validation</h3>
        <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
          @foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach
        </ul>
      </div>
    @endif

    <form action="{{route('annonces.store')}}" method="POST" enctype="multipart/form-data"
          id="formCreate" class="space-y-8">
      @csrf

      {{-- Infos de base --}}
      <section class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Informations de base</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- Titre --}}
          <div class="md:col-span-2">
            <label for="title" class="block text-sm font-medium text-white mb-1">Titre <span class="text-red-400">*</span></label>
            <input id="title" name="title" maxlength="100" required
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-indigo-500">
            <div class="flex justify-between mt-1 text-xs">
              <span class="text-white/60">Soyez attractif</span>
              <span id="count-title" class="text-white/60">0/100</span>
            </div>
          </div>

          {{-- Catégorie --}}
          <div>
            <label for="category_id" class="block text-sm font-medium text-white mb-1">Catégorie <span class="text-red-400">*</span></label>
            <select id="category_id" name="category_id" required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-indigo-500">
              <option value="">-- Choisir --</option>
              @foreach($categories as $c)
                <option value="{{$c->id}}" {{old('category_id')==$c->id ? 'selected':''}} class="bg-gray-800">{{$c->name}}</option>
              @endforeach
            </select>
          </div>

          {{-- Prix --}}
          <div>
            <label for="price" class="block text-sm font-medium text-white mb-1">Prix <span class="text-red-400">*</span></label>
            <input id="price" name="price" type="number" min="0" step="0.01" required
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60">
          </div>

          {{-- Devise --}}
          <div>
            <label for="currency_id" class="block text-sm font-medium text-white mb-1">Devise <span class="text-red-400">*</span></label>
            <select id="currency_id" name="currency_id" required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-indigo-500">
              <option value="">-- Devise --</option>
              @foreach(\App\Models\Currency::orderBy('code')->get() as $cur)
                <option value="{{$cur->id}}" {{old('currency_id')==$cur->id ? 'selected':''}} class="bg-gray-800">{{$cur->code}} ({{$cur->symbol}})</option>
              @endforeach
            </select>
          </div>
        </div>
      </section>

      {{-- Description --}}
      <section class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Description</h2>
        <label for="description" class="block text-sm font-medium text-white mb-1">Description <span class="text-red-400">*</span></label>
        <textarea id="description" name="description" rows="5" maxlength="2000" required
                  class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
        <div class="flex justify-between mt-1 text-xs">
          <span class="text-white/60">Détaillez votre article</span>
          <span id="count-desc" class="text-white/60">0/2000</span>
        </div>
      </section>

      {{-- Localisation & image --}}
      <section class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Localisation & image</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="location" class="block text-sm font-medium text-white mb-1">Ville / région <span class="text-red-400">*</span></label>
            <input id="location" name="location" maxlength="100" required
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60">
          </div>

          {{-- upload image --}}
          <div>
            <label for="image" class="block text-sm font-medium text-white mb-1">Image (optionnel)</label>
            <div id="dropZone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-white/20 border-dashed rounded-xl cursor-pointer">
              <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-white/40" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4h12" stroke-width="2"/></svg>
                <div class="text-white/60 text-sm">JPG, PNG, GIF ≤5MB</div>
                <img id="preview" class="hidden max-h-32 mx-auto rounded">
              </div>
              <input id="image" name="image" type="file" accept="image/*" class="sr-only">
            </div>
          </div>
        </div>
      </section>

      {{-- Bouton final --}}
      <div class="flex justify-end">
        <button type="submit"
                class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold rounded-xl transition transform hover:scale-105">
          Publier
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/annonces/create.js'])
@endpush