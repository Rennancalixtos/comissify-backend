/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.js",
        "./resources/**/*.vue",
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                // Dynamic primary (driven by CSS variable set in layouts)
                primary: {
                    DEFAULT: 'var(--color-primary)',
                    dark:    'var(--color-primary-dark)',
                    light:   'var(--color-primary-light)',
                },
                // Dark theme palette (fixed values)
                d: {
                    bg:     '#141414',
                    card:   '#1C1C1C',
                    card2:  '#212121',
                    input:  '#272727',
                    border: '#2A2A2A',
                    text:   '#F1F1F1',
                    muted:  '#9CA3AF',
                },
                // Kept for any remaining legacy references
                shopee: {
                    50:  '#fff1ee',
                    100: '#ffe0d8',
                    200: '#ffc5b5',
                    300: '#ff9e84',
                    400: '#ff6c4a',
                    500: '#ff4520',
                    600: '#ee2a06',
                    700: '#c71f05',
                    800: '#a31e0b',
                    900: '#861d0f',
                    950: '#490b04',
                },
            },
        },
    },
    plugins: [],
};
