import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(), // 👈 Importante: habilita soporte para .vue
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js', // 👈 Necesario para `<script setup>`
            '@': path.resolve(__dirname, 'resources'),
        },
    },
});
