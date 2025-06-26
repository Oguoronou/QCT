/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#4154f1',
        'primary-hover': '#717ff5',
        dark: '#0a0e34',
      }
    }
  },
  plugins: [],
}