// No longer needed — Tailwind v4 is wired in directly via the
// `@tailwindcss/vite` plugin in vite.config.js, which handles the CSS
// transform (including autoprefixing) without going through PostCSS at
// all. Nothing in this project references this file anymore; it's safe
// to delete. Left as an empty, harmless config in the meantime since
// this tool can't delete files on your machine directly.
export default {
    plugins: {},
};
