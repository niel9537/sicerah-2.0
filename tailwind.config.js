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
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Legacy navy classes now share the SICERAH maroon palette.
                navy: {
                    50: '#FFF1F2', 100: '#FFE0E4', 200: '#F9BBC5',
                    300: '#EC8D9D', 400: '#D85570', 500: '#C22A44',
                    600: '#AD1D30', 700: '#861827', 800: '#741421',
                    900: '#58101B', 950: '#270A10',
                },
                // Gold / Amber Palette matching the official SICERAH Logo
                gold: {
                    50:  '#fdf9ed',
                    100: '#faf0d0',
                    200: '#f5e0a1',
                    300: '#eece6d',
                    400: '#e8bc43',
                    500: '#D4A017',
                    600: '#b38412',
                    700: '#8a6410',
                    800: '#6d4f13',
                    900: '#5b4215',
                    950: '#33230a',
                },
                maroon: {
                    50: '#FFF1F2', 100: '#FFE0E4', 200: '#F9BBC5',
                    300: '#EC8D9D', 400: '#D85570', 500: '#C22A44',
                    600: '#AD1D30', 700: '#861827', 800: '#741421',
                    900: '#58101B', 950: '#270A10',
                },
                brand: {
                    DEFAULT: '#AD1D30', primary: '#AD1D30', hover: '#861827',
                    light: '#FFF1F2', dark: '#58101B', sidebar: '#741421',
                    'sidebar-card': '#58101B', 'sidebar-hover': '#AD1D30',
                },
            },
            boxShadow: {
                'soft': '0 4px 20px -2px rgba(217, 119, 6, 0.12), 0 2px 6px -1px rgba(217, 119, 6, 0.06)',
                'card': '0 4px 24px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02)',
                'card-hover': '0 16px 36px -4px rgba(217, 119, 6, 0.14), 0 4px 12px -2px rgba(15, 23, 42, 0.03)',
                'glow-gold': '0 8px 24px -4px rgba(217, 119, 6, 0.45)',
                'glow-navy': '0 8px 24px -4px rgba(116,20,33,0.4)',
                'glow-sidebar': '0 0 20px rgba(217, 119, 6, 0.35)',
            },
            borderRadius: {
                'xl': '0.875rem',
                '2xl': '1.25rem',
                '3xl': '1.5rem',
                '4xl': '2rem',
            },
            backgroundImage: {
                'brand-gradient': 'linear-gradient(110deg, #68121D 0%, #AD1D30 60%, #861827 100%)',
                'brand-gold':     'linear-gradient(135deg, #b45309 0%, #d97706 45%, #f59e0b 75%, #fbbf24 100%)',
                'brand-soft':     'linear-gradient(135deg, #fef8eb 0%, #ffffff 100%)',
                'hero-gradient':  'linear-gradient(135deg, #270A10 0%, #741421 45%, #AD1D30 100%)',
            },
            animation: {
                'fade-in':  'fadeIn 0.3s ease-in-out',
                'slide-in': 'slideIn 0.25s ease-out',
            },
            keyframes: {
                fadeIn:  { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                slideIn: {
                    '0%': { transform: 'translateX(-10px)', opacity: 0 },
                    '100%': { transform: 'translateX(0)', opacity: 1 },
                },
            },
        },
    },

    plugins: [forms],
};
