import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            // Palette de couleurs personnalisée pour la plateforme d'annonces
            colors: {
                // Couleurs principales avec nuances pour glassmorphisme
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',  // Couleur principale
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
                secondary: {
                    50: '#fdf4ff',
                    100: '#fae8ff',
                    200: '#f5d0fe',
                    300: '#f0abfc',
                    400: '#e879f9',
                    500: '#d946ef',  // Couleur secondaire
                    600: '#c026d3',
                    700: '#a21caf',
                    800: '#86198f',
                    900: '#701a75',
                    950: '#4a044e',
                },
                accent: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',  // Couleur d'accent
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                    950: '#431407',
                },
                // Couleurs pour les états
                success: {
                    50: '#f0fdf4',
                    500: '#22c55e',
                    600: '#16a34a',
                },
                warning: {
                    50: '#fffbeb',
                    500: '#f59e0b',
                    600: '#d97706',
                },
                error: {
                    50: '#fef2f2',
                    500: '#ef4444',
                    600: '#dc2626',
                },
                // Couleurs neutres étendues pour glassmorphisme
                glass: {
                    50: 'rgba(255, 255, 255, 0.9)',
                    100: 'rgba(255, 255, 255, 0.8)',
                    200: 'rgba(255, 255, 255, 0.7)',
                    300: 'rgba(255, 255, 255, 0.6)',
                    400: 'rgba(255, 255, 255, 0.5)',
                    500: 'rgba(255, 255, 255, 0.4)',
                    600: 'rgba(255, 255, 255, 0.3)',
                    700: 'rgba(255, 255, 255, 0.2)',
                    800: 'rgba(255, 255, 255, 0.1)',
                    900: 'rgba(255, 255, 255, 0.05)',
                },
                'glass-dark': {
                    50: 'rgba(0, 0, 0, 0.9)',
                    100: 'rgba(0, 0, 0, 0.8)',
                    200: 'rgba(0, 0, 0, 0.7)',
                    300: 'rgba(0, 0, 0, 0.6)',
                    400: 'rgba(0, 0, 0, 0.5)',
                    500: 'rgba(0, 0, 0, 0.4)',
                    600: 'rgba(0, 0, 0, 0.3)',
                    700: 'rgba(0, 0, 0, 0.2)',
                    800: 'rgba(0, 0, 0, 0.1)',
                    900: 'rgba(0, 0, 0, 0.05)',
                },
            },

            // Polices personnalisées
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },

            // Espacements personnalisés
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },

            // Effets de flou pour glassmorphisme
            backdropBlur: {
                'xs': '2px',
                'sm': '4px',
                'md': '8px',
                'lg': '12px',
                'xl': '16px',
                '2xl': '24px',
                '3xl': '40px',
            },

            // Animations personnalisées
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'fade-in-up': 'fadeInUp 0.5s ease-out',
                'slide-in-right': 'slideInRight 0.3s ease-out',
                'slide-in-left': 'slideInLeft 0.3s ease-out',
                'bounce-gentle': 'bounceGentle 2s infinite',
                'pulse-slow': 'pulse 3s infinite',
                'float': 'float 3s ease-in-out infinite',
                'glow': 'glow 2s ease-in-out infinite alternate',
            },

            // Keyframes pour les animations
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(-20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                bounceGentle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                glow: {
                    '0%': { boxShadow: '0 0 5px rgba(59, 130, 246, 0.5)' },
                    '100%': { boxShadow: '0 0 20px rgba(59, 130, 246, 0.8)' },
                },
            },

            // Ombres personnalisées pour glassmorphisme
            boxShadow: {
                'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.37)',
                'glass-lg': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                'glass-xl': '0 35px 60px -12px rgba(0, 0, 0, 0.3)',
                'inner-glass': 'inset 0 2px 4px 0 rgba(255, 255, 255, 0.1)',
                'glow-primary': '0 0 20px rgba(14, 165, 233, 0.5)',
                'glow-secondary': '0 0 20px rgba(217, 70, 239, 0.5)',
                'glow-accent': '0 0 20px rgba(249, 115, 22, 0.5)',
            },

            // Bordures pour glassmorphisme
            borderRadius: {
                'xl': '1rem',
                '2xl': '1.5rem',
                '3xl': '2rem',
            },

            // Gradients personnalisés
            backgroundImage: {
                'gradient-glass': 'linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%)',
                'gradient-primary': 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)',
                'gradient-secondary': 'linear-gradient(135deg, #d946ef 0%, #c026d3 100%)',
                'gradient-accent': 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
                'gradient-hero': 'linear-gradient(135deg, #0ea5e9 0%, #d946ef 50%, #f97316 100%)',
                'gradient-dark': 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)',
            },

            // Tailles d'écran étendues
            screens: {
                'xs': '475px',
                '3xl': '1600px',
            },

            // Transitions personnalisées
            transitionDuration: {
                '400': '400ms',
                '600': '600ms',
            },

            // Z-index personnalisés
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },
        },
    },

    plugins: [
        forms,
        // Plugin personnalisé pour les utilitaires glassmorphisme
        function ({ addUtilities, theme }) {
            const newUtilities = {
                // Classe principale pour l'effet glassmorphisme
                '.glass': {
                    background: 'rgba(255, 255, 255, 0.1)',
                    backdropFilter: 'blur(10px)',
                    border: '1px solid rgba(255, 255, 255, 0.2)',
                    boxShadow: '0 8px 32px 0 rgba(31, 38, 135, 0.37)',
                },
                '.glass-dark': {
                    background: 'rgba(0, 0, 0, 0.1)',
                    backdropFilter: 'blur(10px)',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                    boxShadow: '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
                },
                '.glass-strong': {
                    background: 'rgba(255, 255, 255, 0.25)',
                    backdropFilter: 'blur(15px)',
                    border: '1px solid rgba(255, 255, 255, 0.3)',
                    boxShadow: '0 12px 40px 0 rgba(31, 38, 135, 0.5)',
                },
                '.glass-subtle': {
                    background: 'rgba(255, 255, 255, 0.05)',
                    backdropFilter: 'blur(5px)',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                    boxShadow: '0 4px 20px 0 rgba(31, 38, 135, 0.2)',
                },
                // Effet de survol pour les éléments glass
                '.glass-hover': {
                    transition: 'all 0.3s ease',
                    '&:hover': {
                        background: 'rgba(255, 255, 255, 0.2)',
                        transform: 'translateY(-2px)',
                        boxShadow: '0 12px 40px 0 rgba(31, 38, 135, 0.5)',
                    },
                },
                // Boutons avec effet glassmorphisme
                '.btn-glass': {
                    background: 'rgba(255, 255, 255, 0.1)',
                    backdropFilter: 'blur(10px)',
                    border: '1px solid rgba(255, 255, 255, 0.2)',
                    transition: 'all 0.3s ease',
                    '&:hover': {
                        background: 'rgba(255, 255, 255, 0.2)',
                        transform: 'translateY(-1px)',
                    },
                },
                // Cards avec effet glassmorphisme
                '.card-glass': {
                    background: 'rgba(255, 255, 255, 0.1)',
                    backdropFilter: 'blur(15px)',
                    border: '1px solid rgba(255, 255, 255, 0.2)',
                    borderRadius: '1rem',
                    boxShadow: '0 8px 32px 0 rgba(31, 38, 135, 0.37)',
                    transition: 'all 0.3s ease',
                    '&:hover': {
                        transform: 'translateY(-5px)',
                        boxShadow: '0 15px 45px 0 rgba(31, 38, 135, 0.5)',
                    },
                },
            }
            addUtilities(newUtilities)
        },
    ],
};