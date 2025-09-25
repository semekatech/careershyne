/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      // Colors
      colors: {
        primary: "#4F46E5",
        "background-light": "#F9FAFB",
        "background-dark": "#111827",
        "surface-light": "#FFFFFF",
        "surface-dark": "#1F2937",
        "text-light": "#1F2937",
        "text-dark": "#F9FAFB",
        "muted-light": "#6B7280",
        "muted-dark": "#9CA3AF",
      },
      // Font
      fontFamily: {
        display: ["Poppins", "sans-serif"],
      },
      // Border radius
      borderRadius: {
        DEFAULT: "0.5rem",
      },
      // Keyframes for animations
      keyframes: {
        scroll: {
          "0%": { transform: "translate(0%)" },
          "95%": { transform: "translate(-95%)" },
        },
        scrollReverse: {
          "0%": { transform: "translate(-55%)" },
          "95%": { transform: "translate(55%)" },
        },
      },
      // Animation utilities
      animation: {
        scroll: "scroll 100s infinite linear",
        scrollReverse: "scrollReverse 100s infinite linear",
      },
    },
  },
  plugins: [require('@tailwindcss/typography')],
};
