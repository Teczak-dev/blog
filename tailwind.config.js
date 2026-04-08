import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class', // Enable dark mode with class strategy

    safelist: [
        // Category color classes with !important modifiers to force application
        '!bg-blue-500', '!text-white',
        '!bg-green-500', '!text-white', 
        '!bg-purple-500', '!text-white',
        '!bg-red-500', '!text-white',
        '!bg-amber-500', '!text-white',
        '!bg-orange-500', '!text-white',
        '!bg-indigo-500', '!text-white',
        '!bg-pink-500', '!text-white',
        '!bg-gray-500', '!text-white',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};