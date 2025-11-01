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
            fontSize: {
                'xs': '0.875rem',   // 14px (元: 12px)
                'sm': '1rem',        // 16px (元: 14px)
                'base': '1.125rem',  // 18px (元: 16px)
                'lg': '1.25rem',     // 20px (元: 18px)
                'xl': '1.5rem',      // 24px (元: 20px)
                '2xl': '1.875rem',   // 30px (元: 24px)
                '3xl': '2.25rem',    // 36px (元: 30px)
            },
        },
    },

    plugins: [forms],
};
