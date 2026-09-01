import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

const vitePort = Number(process.env.VITE_PORT || 5173);
const viteHost = process.env.VITE_HOST || '0.0.0.0';
const hmrHost = process.env.VITE_HMR_HOST || 'localhost';
const hmrPort = Number(process.env.VITE_HMR_PORT || vitePort);
const hmrProtocol = process.env.VITE_HMR_PROTOCOL || 'ws';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
    server: {
        host: viteHost,
        port: vitePort,
        strictPort: true,
        cors: true,
        ...(process.env.VITE_ORIGIN ? { origin: process.env.VITE_ORIGIN } : {}),
        hmr: {
            host: hmrHost,
            port: hmrPort,
            protocol: hmrProtocol,
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        sourcemap: false,
    },
});
