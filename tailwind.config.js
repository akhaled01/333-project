/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.php", "./**/*.html", "./**/*.js", "./index.html"],
  theme: {
    extend: {
      // Custom scrollbar styles
      colors: {
        'scrollbar': {
          track: '#27272a', // zinc-800
          thumb: '#3f3f46', // zinc-700
          'thumb-hover': '#52525b', // zinc-600
        },
      },
    },
  },
  plugins: [
    function({ addBase }) {
      addBase({
        // Firefox
        '*': {
          'scrollbar-width': 'thin',
          'scrollbar-color': '#3f3f46 #27272a', // thumb track
        },
        // Chrome, Edge, Safari
        '::-webkit-scrollbar': {
          width: '8px',
          height: '8px',
        },
        '::-webkit-scrollbar-track': {
          background: '#27272a',
          borderRadius: '4px',
        },
        '::-webkit-scrollbar-thumb': {
          background: '#3f3f46',
          borderRadius: '4px',
        },
        '::-webkit-scrollbar-thumb:hover': {
          background: '#52525b',
        },
      });
    },
  ],
};
