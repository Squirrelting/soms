import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/***/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Ensure text color is strong (black)
                black: '#000000',
            },
        },
    },

    plugins: [
        forms, 
        require('daisyui'),
    ],

    // DaisyUI settings
    daisyui: {
        themes: [
            {
                mytheme: {
                    'primary': '#000000',
                    'text': '#000000', // Default text color set to black
                }
            }
        ],
    },
};
