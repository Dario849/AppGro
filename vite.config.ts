// vite.config.js
import { defineConfig } from 'vite';
// import { fileURLToPath } from 'node:url';
import php from 'vite-plugin-php';
import { resolve } from "path";
import { viteStaticCopy } from 'vite-plugin-static-copy';
// import { ViteEjsPlugin } from 'vite-plugin-ejs';
// import { imagetools } from 'vite-imagetools';
// import { existsSync } from 'node:fs';
// import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ command }) => ({
  base: "/", // ✅ Usar raíz absoluta en producción
  publicDir: "public", // ✅ Mantener carpeta pública clara
  build: {
    outDir: "dist",
    assetsDir: "", // ✅ Evitar subcarpetas innecesarias
    rollupOptions: {
      input: {
        main: resolve(__dirname, "index.php"),
      },
    },
  },
  plugins: [
    // ✅ Solo usar vite-plugin-php en desarrollo (HMR)
    ...(command === "serve" ? [php()] : []),
    viteStaticCopy({
      targets: [
        { src: "public", dest: "" },
        { src: "js", dest: "" },
        { src: "system", dest: "" },
        { src: "configs", dest: "" },
        { src: "vendor", dest: "" },
      ],
    }),
  ],
}));