
module.exports = {
  content: [
    "./admin/**/*.php",
    "./login/**/*.php",
    "./manual/**/*.php",
  ],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        'brand-1': '#1B3FAA',
        'brand-1-20': '#153288',
        'content-1': '#F1F5F8',
      },
      boxShadow: {
        "xl": "0 3px 20px #0000001c"
      },
      maxWidth: {
        "admin": "2000px"
      }
    },
  },
};