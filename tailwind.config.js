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
        },
    },

    plugins: [forms],

    purge: {
        // Your purge configuration
        options: {
            safelist: [
                'border-green-500',  'text-green-500',
                'border-cyan-500',   'text-cyan-500',
                'border-yellow-500', 'text-yellow-500',
                'border-rose-500',   'text-rose-500',
                'border-amber-500',  'text-amber-500',
                'border-blue-500',   'text-blue-500',
                'border-orange-500', 'text-orange-500',
                'border-pink-500',   'text-pink-500',
                // Repeat for each color you use
            ],
        },
    },
    darkMode: 'class',
};
