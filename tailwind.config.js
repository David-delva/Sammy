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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Source Serif 4', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                navy: {
                    50: '#f0f4ff',
                    100: '#e0e9ff',
                    200: '#c7d7fe',
                    400: '#818cf8',
                    500: '#2563eb',
                    600: '#1a56db',
                    700: '#1447c0',
                    800: '#1e3a8a',
                    900: '#0f2d56',
                    950: '#0a1f3d',
                },
                brand: {
                    DEFAULT: '#1a56db',
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    500: '#3b82f6',
                    600: '#1a56db',
                    700: '#1447c0',
                },
            },
            boxShadow: {
                card: '0 1px 3px 0 rgba(0,0,0,.06), 0 1px 2px -1px rgba(0,0,0,.04)',
                'card-md': '0 4px 6px -1px rgba(0,0,0,.05), 0 2px 4px -2px rgba(0,0,0,.04)',
            },
            borderRadius: {
                xl: '0.875rem',
            },
        },
    },

    plugins: [forms],
};
