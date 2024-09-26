const { scale } = require('svelte/transition');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/js/*.js",
    "./assets/vue/*.vue",
    "./templates/**/*.html.twig",
    "./templates/*.html.twig",
    "./assets/vue/*.vue",
    "./src/Form/*.php",

  ],
  theme: {
    colors: {
      'black':'#000000',
      'red': '#FF0000',
      'blue':'#00509E',
      'white':'#FFFFFF',
      'grey':'#CFCFCF',
      'green':'#00CC00',
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
      keyframes:{
      
      },
      animation: {
       
      },
      screens: {
        'sm': '450px',   // Small screens
        'md': '1280px',   // Medium screens
        'lg': '1290px',  // Large screens
        'xl': '1600px'
      },
    }
  },
  plugins: [
  ],
}

