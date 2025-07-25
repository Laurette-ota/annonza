{{--
  Vue de création d'annonce
  Formulaire complet pour publier une nouvelle annonce
  Design avec glassmorphisme et validation côté client
--}}

@extends('layouts.app')

@section('title', 'Publier une annonce')
@section('description', 'Publiez votre annonce gratuitement. Vendez vos objets, proposez vos services ou trouvez ce que vous cherchez.')
@section('keywords', 'publier annonce, vendre, créer annonce, marketplace')

@push('styles')
<style>
    /* Styles pour le formulaire */
    .form-section {
        backdrop-filter: blur(15px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }
    
    /* Styles pour l'upload d'image */
    .image-upload-zone {
        border: 2px dashed rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.05);
    }
    
    .image-upload-zone:hover {
        border-color: rgba(59, 130, 246, 0.6);
        background: rgba(59, 130, 246, 0.1);
    }
    
    .image-upload-zone.dragover {
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.2);
        transform: scale(1.02);
    }
    
    /* Preview de l'image */
    .image-preview {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
    }
    
    .image-preview img {
        transition: transform 0.3s ease;
    }
    
    .image-preview:hover img {
        transform: scale(1.05);
    }
    
    /* Animation pour les champs requis */
    .required-field {
        position: relative;
    }
    
    .required-field::after {
        content: '*';
        color: #ef4444;
        position: absolute;
        top: 8px;
        right: 12px;
        font-size: 18px;
        font-weight: bold;
    }
    
    /* Styles pour les messages d'erreur */
    .error-message {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    
    /* Animation du bouton de soumission */
    .submit-button {
        background: linear-gradient(135deg, #0ea5e9 0%, #d946ef 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .submit-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
    }
    
    .submit-button:active {
        transform: translateY(0);
    }
    
    /* Loader pour le bouton */
    .button-loader {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Compteur de caractères */
    .char-counter {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
    }
    
    .char-counter.warning {
        color: #f59e0b;
    }
    
    .char-counter.danger {
        color: #ef4444;
    }
</style>
@endpush

@section('content')
<div class="pt-20 pb-8"> {{-- Padding pour compenser la navigation fixe --}}
    
    {{-- En-tête de la page --}}
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-4xl mx-auto text-center">
            <div class="fade-in">
                <h1 class="text-4xl font-bold text-white mb-4">
                    <span class="bg-gradient-to-r from-primary-400 to-secondary-400 bg-clip-text text-transparent">
                        Publier une annonce
                    </span>
                </h1>
                <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
                    Remplissez le formulaire ci-dessous pour publier votre annonce. 
                    Plus votre description est détaillée, plus vous aurez de chances de vendre rapidement !
                </p>
            </div>
        </div>
    </div>

    {{-- Formulaire principal --}}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            {{-- Affichage des erreurs de validation --}}
            @if ($errors->any())
                <div class="error-message rounded-2xl p-6 mb-8 fade-in">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <h3 class="text-lg font-semibold">Erreurs de validation</h3>
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('annonces.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  id="annonceForm"
                  class="space-y-8">
                @csrf

                {{-- Section 1: Informations de base --}}
                <div class="form-section rounded-2xl p-6 fade-in">
                    <div class="flex items-center mb-6">
                        <div class="glass-subtle p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Informations de base</h2>
                            <p class="text-white/60">Titre, catégorie et prix de votre annonce</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Titre de l'annonce --}}
                        <div class="md:col-span-2">
                            <label for="titre" class="block text-sm font-medium text-white mb-2">
                                Titre de l'annonce
                                <span class="text-red-400">*</span>
                            </label>
                            <div class="required-field">
                                <input type="text" 
                                       id="titre" 
                                       name="title" 
                                       value="{{ old('title') }}"
                                       maxlength="100"
                                       placeholder="Ex: iPhone 13 Pro Max 256Go, état neuf"
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('titre') border-red-500 @enderror"
                                       required>
                            </div>
                            <div class="flex justify-between mt-1">
                                @error('titre')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @else
                                    <span class="text-white/60 text-sm">Soyez précis et attractif</span>
                                @enderror
                                <span id="titre-counter" class="char-counter">0/100</span>
                            </div>
                        </div>

                        {{-- Catégorie --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-white mb-2">
                                Catégorie
                                <span class="text-red-400">*</span>
                            </label>
                            <div class="required-field">
                                <select id="category_id" 
                                        name="category_id" 
                                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('category_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Choisissez une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}
                                                class="bg-gray-800 text-white">
                                            {{ $category->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @endif
                        </div>

                        {{-- Prix --}}
                        <div>
                            <label for="prix" class="block text-sm font-medium text-white mb-2">
                                Prix (€)
                                <span class="text-red-400">*</span>
                            </label>
                            <div class="required-field">
                                <input type="number" 
                                       id="prix" 
                                       name="price" 
                                       value="{{ old('price') }}"
                                       min="0"
                                       step="0.01"
                                       placeholder="0.00"
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('prix') border-red-500 @enderror"
                                       required>
                            </div>
                            @error('prix')
                                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @else
                                <span class="text-white/60 text-sm mt-1 block">Prix en euros (ex: 299.99)</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Section 2: Description détaillée --}}
                <div class="form-section rounded-2xl p-6 fade-in fade-in-delay-1">
                    <div class="flex items-center mb-6">
                        <div class="glass-subtle p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Description</h2>
                            <p class="text-white/60">Décrivez votre article en détail</p>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">
                            Description complète
                            <span class="text-red-400">*</span>
                        </label>
                        <div class="required-field">
                            <textarea id="description" 
                                      name="description" 
                                      rows="6"
                                      maxlength="2000"
                                      placeholder="Décrivez votre article : état, caractéristiques, raison de la vente, etc. Plus votre description est détaillée, plus vous aurez de chances de vendre !"
                                      class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none @error('description') border-red-500 @enderror"
                                      required>{{ old('description') }}</textarea>
                        </div>
                        <div class="flex justify-between mt-1">
                            @error('description')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @else
                                <span class="text-white/60 text-sm">Mentionnez l'état, les caractéristiques importantes, etc.</span>
                            @enderror
                            <span id="description-counter" class="char-counter">0/2000</span>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Localisation --}}
                <div class="form-section rounded-2xl p-6 fade-in fade-in-delay-2">
                    <div class="flex items-center mb-6">
                        <div class="glass-subtle p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Localisation</h2>
                            <p class="text-white/60">Où se trouve votre article ?</p>
                        </div>
                    </div>

                    <div>
                        <label for="localisation" class="block text-sm font-medium text-white mb-2">
                            Ville ou région
                            <span class="text-red-400">*</span>
                        </label>
                        <div class="required-field">
                            <input type="text" 
                                   id="localisation" 
                                   name="location" 
                                   value="{{ old('location') }}"
                                   maxlength="100"
                                   placeholder="Ex: Paris, Lyon, Marseille, Bordeaux..."
                                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('localisation') border-red-500 @enderror"
                                   required>
                        </div>
                        @error('localisation')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @else
                            <span class="text-white/60 text-sm mt-1 block">Indiquez votre ville ou région pour faciliter la vente</span>
                        @endif
                    </div>
                </div>

                {{-- Section 4: Image --}}
                <div class="form-section rounded-2xl p-6 fade-in fade-in-delay-3">
                    <div class="flex items-center mb-6">
                        <div class="glass-subtle p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Photo</h2>
                            <p class="text-white/60">Ajoutez une photo de votre article (optionnel)</p>
                        </div>
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-white mb-2">
                            Image de l'annonce
                        </label>
                        
                        {{-- Zone de drop pour l'image --}}
                        <div id="imageDropZone" 
                             class="image-upload-zone rounded-2xl p-8 text-center cursor-pointer">
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="handleImageSelect(this)">
                            
                            <div id="uploadPrompt" class="space-y-4">
                                <svg class="w-16 h-16 text-white/40 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <div>
                                    <p class="text-white font-medium mb-2">Cliquez pour sélectionner une image</p>
                                    <p class="text-white/60 text-sm">ou glissez-déposez votre fichier ici</p>
                                    <p class="text-white/40 text-xs mt-2">JPG, PNG, GIF - Max 5MB</p>
                                </div>
                            </div>
                            
                            {{-- Preview de l'image --}}
                            <div id="imagePreview" class="hidden">
                                <div class="image-preview relative inline-block">
                                    <img id="previewImage" src="" alt="Aperçu" class="max-w-full max-h-64 rounded-xl">
                                    <button type="button" 
                                            onclick="removeImage()"
                                            class="absolute top-2 right-2 glass-strong p-2 rounded-lg text-white hover:bg-red-500/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-white/60 text-sm mt-2">Cliquez sur l'image pour la changer</p>
                            </div>
                        </div>
                        
                        @error('image')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @endif
                    </div>
                </div>

                {{-- Section 5: Validation et soumission --}}
                <div class="form-section rounded-2xl p-6 fade-in fade-in-delay-4">
                    <div class="flex items-center mb-6">
                        <div class="glass-subtle p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Finalisation</h2>
                            <p class="text-white/60">Vérifiez vos informations et publiez</p>
                        </div>
                    </div>

                    {{-- Récapitulatif --}}
                    <div class="glass-subtle rounded-xl p-4 mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Récapitulatif</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-white/60">Titre :</span>
                                <span id="recap-titre" class="text-white ml-2">-</span>
                            </div>
                            <div>
                                <span class="text-white/60">Prix :</span>
                                <span id="recap-prix" class="text-white ml-2">-</span>
                            </div>
                            <div>
                                <span class="text-white/60">Catégorie :</span>
                                <span id="recap-categorie" class="text-white ml-2">-</span>
                            </div>
                            <div>
                                <span class="text-white/60">Localisation :</span>
                                <span id="recap-localisation" class="text-white ml-2">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Conditions d'utilisation --}}
                    <div class="mb-6">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   id="terms" 
                                   name="terms" 
                                   required
                                   class="mt-1 w-4 h-4 text-primary-600 bg-white/10 border-white/20 rounded focus:ring-primary-500 focus:ring-2">
                            <span class="text-white/80 text-sm">
                                J'accepte les 
                                <a href="#" class="text-primary-400 hover:text-primary-300 underline">conditions d'utilisation</a> 
                                et je certifie que les informations fournies sont exactes. 
                                Je m'engage à respecter les règles de la plateforme.
                                <span class="text-red-400">*</span>
                            </span>
                        </label>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button" 
                                onclick="window.history.back()"
                                class="flex-1 glass-subtle border border-white/20 text-white font-medium py-3 px-6 rounded-xl hover:bg-white/15 transition-all duration-300">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                <span>Retour</span>
                            </div>
                        </button>
                        
                        <button type="submit" 
                                id="submitButton"
                                class="flex-1 submit-button text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="button-loader"></div>
                                <svg id="submitIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span id="submitText">Publier l'annonce</span>
                            </div>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Variables globales
    let imageFile = null;

    // Initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
        setupImageUpload();
        setupFormValidation();
        setupCharacterCounters();
        setupRecapitulatif();
    });

    // Initialisation du formulaire
    function initializeForm() {
        // Animation d'apparition des sections
        const sections = document.querySelectorAll('.form-section');
        sections.forEach((section, index) => {
            setTimeout(() => {
                section.classList.add('fade-in');
            }, index * 200);
        });
    }

    // Configuration de l'upload d'image
    function setupImageUpload() {
        const dropZone = document.getElementById('imageDropZone');
        const fileInput = document.getElementById('image');

        // Événements de clic
        dropZone.addEventListener('click', () => fileInput.click());

        // Événements de drag & drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleImageFile(files[0]);
            }
        });
    }

    // Gestion de la sélection d'image
    function handleImageSelect(input) {
        if (input.files && input.files[0]) {
            handleImageFile(input.files[0]);
        }
    }

    // Traitement du fichier image
    function handleImageFile(file) {
        // Validation du type de fichier
        if (!file.type.startsWith('image/')) {
            showToast('Veuillez sélectionner un fichier image valide.', 'error');
            return;
        }

        // Validation de la taille (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showToast('L\'image ne doit pas dépasser 5MB.', 'error');
            return;
        }

        imageFile = file;

        // Affichage de l'aperçu
        const reader = new FileReader();
        reader.onload = function(e) {
            const uploadPrompt = document.getElementById('uploadPrompt');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');

            uploadPrompt.classList.add('hidden');
            imagePreview.classList.remove('hidden');
            previewImage.src = e.target.result;
        };
        reader.readAsDataURL(file);

        showToast('Image ajoutée avec succès !', 'success');
    }

    // Suppression de l'image
    function removeImage() {
        const fileInput = document.getElementById('image');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const imagePreview = document.getElementById('imagePreview');

        fileInput.value = '';
        imageFile = null;
        uploadPrompt.classList.remove('hidden');
        imagePreview.classList.add('hidden');

        showToast('Image supprimée.', 'info');
    }

    // Configuration des compteurs de caractères
    function setupCharacterCounters() {
        const fields = [
            { input: 'titre', counter: 'titre-counter', max: 100 },
            { input: 'description', counter: 'description-counter', max: 2000 }
        ];

        fields.forEach(field => {
            const input = document.getElementById(field.input);
            const counter = document.getElementById(field.counter);

            input.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = `${length}/${field.max}`;

                // Changement de couleur selon le pourcentage
                counter.classList.remove('warning', 'danger');
                if (length > field.max * 0.9) {
                    counter.classList.add('danger');
                } else if (length > field.max * 0.7) {
                    counter.classList.add('warning');
                }
            });

            // Initialisation
            input.dispatchEvent(new Event('input'));
        });
    }

    // Configuration du récapitulatif en temps réel
    function setupRecapitulatif() {
        const fields = {
            'titre': 'recap-titre',
            'prix': 'recap-prix',
            'category_id': 'recap-categorie',
            'localisation': 'recap-localisation'
        };

        Object.keys(fields).forEach(fieldName => {
            const input = document.getElementById(fieldName);
            const recap = document.getElementById(fields[fieldName]);

            input.addEventListener('input', function() {
                let value = this.value || '-';
                
                if (fieldName === 'prix' && value !== '-') {
                    value = parseFloat(value).toLocaleString('fr-FR', {
                        style: 'currency',
                        currency: 'EUR'
                    });
                } else if (fieldName === 'category_id' && value !== '-') {
                    value = this.options[this.selectedIndex].text;
                }

                recap.textContent = value;
            });

            // Initialisation
            input.dispatchEvent(new Event('input'));
        });
    }

    // Validation du formulaire
    function setupFormValidation() {
        const form = document.getElementById('annonceForm');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                submitForm();
            }
        });
    }

    // Validation des champs
    function validateForm() {
        let isValid = true;
        const requiredFields = ['titre', 'category_id', 'prix', 'description', 'localisation'];

        // Vérification des champs requis
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            const value = field.value.trim();

            if (!value) {
                showFieldError(field, 'Ce champ est requis.');
                isValid = false;
            } else {
                clearFieldError(field);
            }
        });

        // Validation spécifique du prix
        const prixField = document.getElementById('prix');
        const prix = parseFloat(prixField.value);
        if (prix < 0) {
            showFieldError(prixField, 'Le prix ne peut pas être négatif.');
            isValid = false;
        }

        // Vérification des conditions
        const termsCheckbox = document.getElementById('terms');
        if (!termsCheckbox.checked) {
            showToast('Vous devez accepter les conditions d\'utilisation.', 'error');
            isValid = false;
        }

        return isValid;
    }

    // Affichage d'erreur sur un champ
    function showFieldError(field, message) {
        field.classList.add('border-red-500');
        
        // Suppression de l'ancien message d'erreur
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }

        // Ajout du nouveau message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-400 text-sm mt-1';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    // Suppression d'erreur sur un champ
    function clearFieldError(field) {
        field.classList.remove('border-red-500');
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Soumission du formulaire
    function submitForm() {
        const submitButton = document.getElementById('submitButton');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');
        const buttonLoader = submitButton.querySelector('.button-loader');

        // Animation du bouton
        submitButton.disabled = true;
        submitIcon.style.display = 'none';
        buttonLoader.style.display = 'block';
        submitText.textContent = 'Publication en cours...';

        // Soumission du formulaire
        document.getElementById('annonceForm').submit();
    }

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

    // Sauvegarde automatique en local storage (optionnel)
    function setupAutoSave() {
        const fields = ['titre', 'category_id', 'prix', 'description', 'localisation'];
        
        fields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            
            // Restaurer les données sauvegardées
            const savedValue = localStorage.getItem(`annonce_draft_${fieldName}`);
            if (savedValue && !field.value) {
                field.value = savedValue;
                field.dispatchEvent(new Event('input'));
            }

            // Sauvegarder lors de la saisie
            field.addEventListener('input', function() {
                localStorage.setItem(`annonce_draft_${fieldName}`, this.value);
            });
        });
    }

    // Nettoyage du brouillon après soumission réussie
    function clearDraft() {
        const fields = ['titre', 'category_id', 'prix', 'description', 'localisation'];
        fields.forEach(fieldName => {
            localStorage.removeItem(`annonce_draft_${fieldName}`);
        });
    }

    // Activation de la sauvegarde automatique
    setupAutoSave();
</script>
@endpush