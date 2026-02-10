import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath } from 'url'

// https://vite.dev/config/
export default defineConfig({
	plugins: [vue()],
	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./src', import.meta.url))
		}
	},
	server: {
		host: '0.0.0.0',
		port: 5173,
		watch: {
			usePolling: true // Required for Docker environment to detect file changes
		}
	},
	// Build configuration
	build: {
		outDir: 'dist', // Build to dist/ first
		emptyOutDir: true,
		manifest: true,
		rollupOptions: {
			input: './src/main.ts',
			output: {
				manualChunks: {
					'vendor': ['vue', 'vue-router', 'pinia'],
					'primevue': ['primevue/config', 'primevue/button', 'primevue/inputtext', 'primevue/dropdown', 'primevue/datatable'],
					'i18n': ['vue-i18n']
				}
			}
		},
		chunkSizeWarningLimit: 1000 // Increase warning limit to 1MB
	},
	css: {
		preprocessorOptions: {
			scss: {
				additionalData: `@use "@/assets/styles/variables.scss" as *;`
			}
		}
	}
})