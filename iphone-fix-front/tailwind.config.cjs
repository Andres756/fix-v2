/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
    "./pages/**/*.{vue,js,ts,jsx,tsx}",
    "./components/**/*.{vue,js,ts,jsx,tsx}",
    "./**/*.vue"  // Esta línea captura TODOS los archivos Vue
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}