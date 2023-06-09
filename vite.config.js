import {fileURLToPath} from "url"
import {dirname} from "path"
import {defineConfig} from "vite"

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)

export default defineConfig({
    build: {
        lib: {
            entry: `${__dirname}/index.js`,
            name: "Modal Form Brochure",
            formats: ["es"]
        },
        rollupOptions: {
            output: {
                entryFileNames: "[name].mjs",
                chunkFileNames: "[name].mjs",
                assetFileNames: "[name].[ext]"
            }
        }
    }
})
