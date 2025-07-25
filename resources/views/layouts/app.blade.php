<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Titre dynamique de la page -->
    <title>@yield('title', 'Plateforme d\'Annonces') - {{ config('app.name', 'AnnoncesHub') }}</title>

    <!-- Meta descriptions pour SEO -->
    <meta name="description" content="@yield('description', 'Plateforme moderne de publication et recherche d\'annonces. Trouvez ou publiez des annonces facilement avec notre interface intuitive.')">
    <meta name="keywords" content="@yield('keywords', 'annonces, petites annonces, vente, achat, services, marketplace')">
    
    <!-- Open Graph pour réseaux sociaux -->
    <meta property="og:title" content="@yield('og_title', 'Plateforme d\'Annonces')">
    <meta property="og:description" content="@yield('og_description', 'Plateforme moderne de publication et recherche d\'annonces')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Polices Google Fonts pour un design moderne -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icônes Heroicons pour l'interface -->
    <script src="https://unpkg.com/heroicons@2.0.18/24/outline/index.js" type="module"></script>
    <script src="https://unpkg.com/heroicons@2.0.18/24/solid/index.js" type="module"></script>

    <!-- Scripts Vite pour TailwindCSS et JavaScript -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles personnalisés pour glassmorphisme -->
    <style>
        /* Arrière-plan animé avec gradient */
        .animated-bg {
            background: linear-gradient(-45deg, #0ea5e9, #d946ef, #f97316, #22c55e);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Particules flottantes pour l'arrière-plan */
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-shape:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-shape:nth-child(2) {
            width: 120px;
            height: 120px;
            left: 20%;
            animation-delay: 2s;
        }

        .floating-shape:nth-child(3) {
            width: 60px;
            height: 60px;
            right: 10%;
            animation-delay: 4s;
        }

        .floating-shape:nth-child(4) {
            width: 100px;
            height: 100px;
            right: 20%;
            animation-delay: 1s;
        }

        /* Effet de scroll smooth */
        html {
            scroll-behavior: smooth;
        }

        /* Personnalisation de la scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Animation d'apparition pour les éléments */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .fade-in-delay-1 { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }
        .fade-in-delay-3 { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <!-- Scripts additionnels pour la page courante -->
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Arrière-plan animé avec glassmorphisme -->
    <div class="fixed inset-0 animated-bg"></div>
    
    <!-- Formes flottantes décoratives -->
    <div class="floating-shapes">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>

    <!-- Conteneur principal de l'application -->
    <div class="relative min-h-screen">
        
        <!-- Navigation principale -->
        @include('layouts.navigation')

        <!-- Messages flash (succès, erreur, info) -->
        @if(session('success') || session('error') || session('info') || session('warning'))
            <div class="fixed top-20 right-4 z-50 space-y-2" id="flash-messages">
                @if(session('success'))
                    <div class="glass-strong rounded-xl p-4 text-green-800 border-l-4 border-green-500 fade-in">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="glass-strong rounded-xl p-4 text-red-800 border-l-4 border-red-500 fade-in">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="glass-strong rounded-xl p-4 text-blue-800 border-l-4 border-blue-500 fade-in">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ session('info') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="glass-strong rounded-xl p-4 text-yellow-800 border-l-4 border-yellow-500 fade-in">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- En-tête de page avec effet glassmorphisme -->
        @if (isset($header))
            <header class="glass-subtle border-b border-white/20 backdrop-blur-xl">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="fade-in">
                        {{ $header }}
                    </div>
                </div>
            </header>
        @endif

        <!-- Contenu principal de la page -->
        <main class="relative z-10">
            <!-- Contenu de la page avec animation d'apparition -->
            <div class="fade-in">
                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </main>

        <!-- Pied de page avec glassmorphisme -->
        <footer class="glass-subtle border-t border-white/20 backdrop-blur-xl mt-16">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Logo et description -->
                    <div class="col-span-1 md:col-span-2 fade-in">
                        <h3 class="text-xl font-bold text-white mb-4">{{ config('app.name', 'AnnoncesHub') }}</h3>
                        <p class="text-white/80 mb-4">
                            Plateforme moderne de publication et recherche d'annonces. 
                            Trouvez ce que vous cherchez ou vendez facilement vos articles.
                        </p>
                        <div class="flex space-x-4">
                            <!-- Liens réseaux sociaux avec effet glassmorphisme -->
                            <a href="#" class="btn-glass p-2 rounded-lg text-white hover:text-primary-400 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="btn-glass p-2 rounded-lg text-white hover:text-primary-400 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="btn-glass p-2 rounded-lg text-white hover:text-primary-400 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Liens rapides -->
                    <div class="fade-in fade-in-delay-1">
                        <h4 class="text-lg font-semibold text-white mb-4">Liens rapides</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="text-white/80 hover:text-white transition-colors">Accueil</a></li>
                            <li><a href="{{ route('categories.index') }}" class="text-white/80 hover:text-white transition-colors">Catégories</a></li>
                            @auth
                                <li><a href="{{ route('annonces.create') }}" class="text-white/80 hover:text-white transition-colors">Publier</a></li>
                                <li><a href="{{ route('favorites.index') }}" class="text-white/80 hover:text-white transition-colors">Favoris</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div class="fade-in fade-in-delay-2">
                        <h4 class="text-lg font-semibold text-white mb-4">Contact</h4>
                        <ul class="space-y-2 text-white/80">
                            <li>Email: contact@annonceshub.com</li>
                            <li>Tél: +33 1 23 45 67 89</li>
                            <li>Adresse: 123 Rue de la Paix, Paris</li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-white/20 mt-8 pt-8 text-center">
                    <p class="text-white/60">
                        © {{ date('Y') }} {{ config('app.name', 'AnnoncesHub') }}. Tous droits réservés.
                        Développé avec ❤️ et Laravel.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts JavaScript -->
    <script>
        // Animation des messages flash - disparition automatique après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.getElementById('flash-messages');
            if (flashMessages) {
                setTimeout(() => {
                    flashMessages.style.opacity = '0';
                    flashMessages.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        flashMessages.remove();
                    }, 300);
                }, 5000);
            }

            // Animation d'apparition progressive des éléments
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

            // Observer tous les éléments avec la classe 'animate-on-scroll'
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });

        // Fonction utilitaire pour les requêtes AJAX avec CSRF
        window.ajaxRequest = function(url, method = 'GET', data = {}) {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            };

            if (method !== 'GET') {
                options.body = JSON.stringify(data);
            }

            return fetch(url, options);
        };
    </script>

    <!-- Scripts additionnels pour la page courante -->
    @stack('scripts')
</body>
</html>