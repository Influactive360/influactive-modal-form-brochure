import { fileURLToPath } from "url"
import { dirname } from "path"
import { defineConfig } from "vite"

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)

export default defineConfig({
  build: {
    lib: {
      entry: `${__dirname}/index.js`,
      name: "Modal Form Brochure",
      formats: ["es"] // Spécifiez uniquement 'es' pour n'obtenir que le format ESM (.mjs)
    },
    rollupOptions: {
      output: {
        entryFileNames: "[name].mjs", // Spécifiez explicitement .mjs comme extension de sortie
        chunkFileNames: "[name].mjs",
        assetFileNames: "[name].mjs"
      }
    }
  }
})
