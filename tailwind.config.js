/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./resources/**/*.{js,ts,jsx,tsx}"],
  plugins: [require("@tailwindcss/typography"), require("tailwindcss-animate")],
  theme: {
    fontFamily: {
      sans: ["Inter", "sans-serif"],
    },
  },
};
