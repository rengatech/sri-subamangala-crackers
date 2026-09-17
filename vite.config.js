import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true
        }
    },
    build: {
        target: 'es2020',
        cssMinify: 'lightningcss',
        rollupOptions: {
            output: {
                manualChunks: {
                    vue: ['vue', '@inertiajs/vue3'],
                },
            },
        },
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
