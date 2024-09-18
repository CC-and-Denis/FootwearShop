/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/js/*.js",
    "./assets/vue/*.vue",
    "./templates/**/*.html.twig",
    "./templates/*.html.twig",

  ],
  theme: {
    colors: {
      'black':'#000000',
      'red': '#FF0000',
      'blue':'#00509E',
      'white':'#FFFFFF',
      'grey':'#CFCFCF',
      'transparent':'rgba(0,0,0,0)',
      'semi-transparent-1': 'rgba(255,255,255,0.05)',
      'semi-transparent-2': 'rgba(255,255,255,0.2)',
      'semi-transparent-3': 'rgba(255,255,255,0.4)',
      'shadow': 'rgba(0,0,0,0.05)',
    },
    fontFamily: {
      lato: ['lato', 'sans-serif'],
    },
    extend: {
      screens: {
        'sm': '450px',   // Small screens
        'md': '960px',   // Medium screens
        'lg': '1024px',  // Large screens
      },
    }
  },
  plugins: [
  ],
}

