import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                serif: ['Source Serif 4', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                navy: {
                    50: '#eef1ff',
                    100: '#dfe5ff',
                    200: '#c7d1ff',
                    400: '#6678d8',
                    500: '#455bc1',
                    600: '#243593',
                    700: '#1f2b85',
                    800: '#1A237E',
                    900: '#152067',
                    950: '#0f184d',
                },
                brand: {
                    DEFAULT: '#1A237E',
                    50: '#eef1ff',
                    100: '#dde4ff',
                    200: '#c5d0ff',
                    300: '#a6b5ff',
                    400: '#7489e2',
                    500: '#455bc1',
                    600: '#1A237E',
                    700: '#152067',
                    800: '#10194f',
                    900: '#0b1236',
                },
                premium: {
                    DEFAULT: '#C5A059',
                    50: '#fbf7ef',
                    100: '#f4ead5',
                    200: '#ead7ae',
                    300: '#ddc282',
                    400: '#d1af67',
                    500: '#C5A059',
                    600: '#a88646',
                    700: '#8a6d39',
                    800: '#6e582f',
                    900: '#594826',
                },
            },
            boxShadow: {
                card: '0 18px 42px rgba(26, 35, 126, 0.08)',
                'card-md': '0 24px 60px rgba(26, 35, 126, 0.12)',
            },
            borderRadius: {
                xl: '0.95rem',
            },
        },
    },

    plugins: [forms],
};