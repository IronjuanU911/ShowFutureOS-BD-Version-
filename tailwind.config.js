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
                sf: {
                    purple: '#511560',
                    'purple-hover': '#41104d',
                    'purple-dark': '#2c0b34',
                    'purple-light': '#6f1d83',
                    'purple-50': '#faf4fb',
                    'purple-100': '#f3e6f6',
                    'purple-200': '#e8ceef',
                    'purple-900': '#380f42',
                    silver: '#C6C6C6',
                    'silver-light': '#f4f4f4',
                    'silver-dark': '#9e9e9e',
                }
            }
        },
    },

    plugins: [forms],
};
