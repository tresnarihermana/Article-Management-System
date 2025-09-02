/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}", 
    "./resources/**/*.blade.php",    // kalau pakai Laravel
    "./resources/**/*.js",
    "./resources/**/*.vue"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: "#f5faff",
          100: "#e0f2ff",
          200: "#b9e4ff",
          300: "#7fd1ff",
          400: "#38b6ff",
          500: "#009eff",
          600: "#007fd1",
          700: "#0063a3",
          800: "#004d7d",
          900: "#003a5c",
        },
      },
      fontFamily: {
        sans: ["Inter", "ui-sans-serif", "system-ui"],
        serif: ["Merriweather", "ui-serif", "Georgia"],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/typography'),
    require('flowbite-typography'),
  ],
}
