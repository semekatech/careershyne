module.exports = {
  darkMode: 'class',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      keyframes: {
        scroll: {
          "0%": { transform: "translate(0%)" },
          "95%": { transform: "translate(-95%)" }
        },
        scrollReverse: {
          "0%": { transform: "translate(-55%)" },
          "95%": { transform: "translate(55%)" }
        }
      },
      animation: {
        scroll: 'scroll 100s infinite linear',
        scrollReverse: 'scrollReverse 100s infinite linear',
      }
    },
  },
  plugins: [require('@tailwindcss/typography')],
}
