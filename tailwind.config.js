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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#EDF6F6',
                    100: '#D3E9E8',
                    300: '#8FC4C0',
                    500: '#2C7A73',
                    600: '#1F6158',
                    700: '#174A44',
                    900: '#0C2825',
                },
            },
        },
    },

    plugins: [forms],
};
