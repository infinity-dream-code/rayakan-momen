/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/landing.blade.php',
        './resources/views/partials/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                charcoal: '#12161f',
                navy: {
                    DEFAULT: '#1a2234',
                    soft: '#243049',
                    deep: '#0e1320',
                },
                gold: {
                    DEFAULT: '#c9a84c',
                    light: '#e8d5a3',
                    dark: '#a8843a',
                },
                champagne: '#d4b896',
                blush: '#f3e8e4',
                ivory: '#faf7f2',
            },
            fontFamily: {
                display: ['"Playfair Display"', 'serif'],
                sans: ['Poppins', 'sans-serif'],
            },
        },
    },
    corePlugins: {
        preflight: false,
    },
    plugins: [],
};
