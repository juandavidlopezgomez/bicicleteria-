/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#1DC763',
                secondary: '#F5F7FA',
                accent1: '#36B37E',
                accent2: '#00875A',
                textPrimary: '#333333',
                textSecondary: '#6E7687',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};