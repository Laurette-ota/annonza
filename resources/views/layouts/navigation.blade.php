{{-- 
  Navigation principale avec effet glassmorphisme
  Cette navigation est responsive et inclut tous les liens principaux de l'application
--}}
<nav x-data="{ open: false, searchOpen: false }" class="glass-strong border-b border-white/20 backdrop-blur-xl fixed w-full top-0 z-50">
    <!-- Menu de navigation principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Section gauche : Logo et liens principaux -->
            <div class="flex items-center">
                
                <!-- Logo de l'application avec effet glassmorphisme -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                        <!-- Icône du logo avec animation -->
                        <div class="glass-hover p-2 rounded-xl group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                        <!-- Nom de l'application -->
                        <span class="text-xl font-bold text-white hidden sm:block">
                            {{ config('app.name', 'AnnoncesHub') }}
                        </span>
                    </a>
                </div>

                <!-- Liens de navigation principaux (desktop) -->
                <div class="hidden md:flex md:items-center md:space-x-1 md:ml-8">
                    
                    <!-- Lien Accueil -->
                    <a href="{{ route('home') }}" 
                       class="btn-glass px-4 py-2 rounded-lg text-white hover:text-primary-200 transition-all duration-300 
                              {{ request()->routeIs('home') ? 'bg-white/20 text-primary-200' : '' }}">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Accueil</span>
                        </div>
                    </a>

                    <!-- Lien Catégories -->
                    <a href="{{ route('categories.index') }}" 
                       class="btn-glass px-4 py-2 rounded-lg text-white hover:text-primary-200 transition-all duration-300
                              {{ request()->routeIs('categories.*') ? 'bg-white/20 text-primary-200' : '' }}">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                            </svg>
                            <span>Catégories</span>
                        </div>
                    </a>

                    <!-- Liens pour utilisateurs connectés -->
                    @auth
                        <!-- Lien Mes Favoris -->
                        <a href="{{ route('favorites.index') }}" 
                           class="btn-glass px-4 py-2 rounded-lg text-white hover:text-primary-200 transition-all duration-300
                                  {{ request()->routeIs('favorites.*') ? 'bg-white/20 text-primary-200' : '' }}">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>Favoris</span>
                            </div>
                        </a>

                        <!-- Lien Mes Annonces -->
                        <a href="{{ route('annonces.my-annonces') }}" 
                           class="btn-glass px-4 py-2 rounded-lg text-white hover:text-primary-200 transition-all duration-300
                                  {{ request()->routeIs('annonces.my-annonces') ? 'bg-white/20 text-primary-200' : '' }}">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Mes Annonces</span>
                            </div>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Section droite : Recherche et menu utilisateur -->
            <div class="flex items-center space-x-4">
                
                <!-- Barre de recherche (desktop) -->
                <div class="hidden lg:block">
                    <form action="{{ route('home') }}" method="GET" class="relative">
                        <div class="glass-subtle rounded-xl overflow-hidden">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Rechercher des annonces..." 
                                   class="bg-transparent border-0 text-white placeholder-white/60 px-4 py-2 w-64 focus:ring-0 focus:outline-none">
                            <button type="submit" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-white/80 hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Bouton de recherche mobile -->
                <button @click="searchOpen = !searchOpen" 
                        class="lg:hidden btn-glass p-2 rounded-lg text-white hover:text-primary-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <!-- Bouton Publier une annonce -->
                @auth
                    <a href="{{ route('annonces.create') }}" 
                       class="bg-gradient-primary hover:bg-gradient-secondary px-4 py-2 rounded-xl text-white font-medium 
                              transition-all duration-300 hover:scale-105 hover:shadow-glow-primary">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden sm:block">Publier</span>
                        </div>
                    </a>
                @endauth

                <!-- Menu utilisateur ou liens de connexion -->
                @auth
                    <!-- Dropdown menu utilisateur connecté -->
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" 
                                class="btn-glass p-2 rounded-xl text-white hover:text-primary-200 transition-all duration-300">
                            <div class="flex items-center space-x-2">
                                <!-- Avatar utilisateur -->
                                <div class="w-8 h-8 bg-gradient-primary rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span class="hidden md:block">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        <!-- Menu dropdown -->
                        <div x-show="userMenuOpen" 
                             @click.outside="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 glass-strong rounded-xl shadow-glass-lg z-50">
                            
                            <!-- Informations utilisateur -->
                            <div class="px-4 py-3 border-b border-white/20">
                                <p class="text-sm text-white font-medium">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-white/60">{{ Auth::user()->email }}</p>
                            </div>

                            <!-- Liens du menu -->
                            <div class="py-2">
                                <a href="{{ route('dashboard') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                    </svg>
                                    Tableau de bord
                                </a>
                                
                                <a href="{{ route('profile.edit') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Mon Profil
                                </a>

                                <div class="border-t border-white/20 my-2"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors text-left">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Se déconnecter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Liens de connexion pour utilisateurs non connectés -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" 
                           class="btn-glass px-4 py-2 rounded-lg text-white hover:text-primary-200 transition-all duration-300">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-gradient-primary hover:bg-gradient-secondary px-4 py-2 rounded-xl text-white font-medium 
                                  transition-all duration-300 hover:scale-105 hover:shadow-glow-primary">
                            Inscription
                        </a>
                    </div>
                @endauth

                <!-- Bouton menu mobile -->
                <button @click="open = !open" 
                        class="md:hidden btn-glass p-2 rounded-lg text-white hover:text-primary-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Barre de recherche mobile -->
    <div x-show="searchOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="lg:hidden border-t border-white/20 px-4 py-3">
        <form action="{{ route('home') }}" method="GET">
            <div class="glass-subtle rounded-xl overflow-hidden">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Rechercher des annonces..." 
                       class="bg-transparent border-0 text-white placeholder-white/60 px-4 py-3 w-full focus:ring-0 focus:outline-none">
            </div>
        </form>
    </div>

    <!-- Menu de navigation mobile -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="md:hidden border-t border-white/20">
        
        <!-- Liens de navigation mobile -->
        <div class="px-4 py-3 space-y-2">
            
            <a href="{{ route('home') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors
                      {{ request()->routeIs('home') ? 'bg-white/20' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Accueil</span>
            </a>

            <a href="{{ route('categories.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors
                      {{ request()->routeIs('categories.*') ? 'bg-white/20' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                </svg>
                <span>Catégories</span>
            </a>

            @auth
                <a href="{{ route('favorites.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors
                          {{ request()->routeIs('favorites.*') ? 'bg-white/20' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span>Mes Favoris</span>
                </a>

                <a href="{{ route('annonces.my-annonces') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors
                          {{ request()->routeIs('annonces.my-annonces') ? 'bg-white/20' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Mes Annonces</span>
                </a>

                <a href="{{ route('annonces.create') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-gradient-primary text-white hover:bg-gradient-secondary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Publier une annonce</span>
                </a>

                <!-- Informations utilisateur mobile -->
                <div class="border-t border-white/20 mt-4 pt-4">
                    <div class="px-4 py-2">
                        <p class="text-sm text-white font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-white/60">{{ Auth::user()->email }}</p>
                    </div>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Mon Profil</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-colors text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Se déconnecter</span>
                        </button>
                    </form>
                </div>
            @else
                <!-- Liens de connexion mobile -->
                <div class="border-t border-white/20 mt-4 pt-4 space-y-2">
                    <a href="{{ route('login') }}" 
                       class="flex items-center justify-center px-4 py-3 rounded-lg text-white border border-white/20 hover:bg-white/10 transition-colors">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" 
                       class="flex items-center justify-center px-4 py-3 rounded-lg bg-gradient-primary text-white hover:bg-gradient-secondary transition-colors">
                        Inscription
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>

{{-- Script Alpine.js pour la navigation --}}
<script>
    // Fermer les menus quand on clique ailleurs
    document.addEventListener('click', function(event) {
        // Fermer le menu utilisateur si on clique ailleurs
        if (!event.target.closest('[x-data]')) {
            // Déclencher la fermeture des menus ouverts
            window.dispatchEvent(new CustomEvent('click-outside'));
        }
    });
</script>