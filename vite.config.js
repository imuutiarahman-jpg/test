import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Membuka akses ke semua interface jaringan
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'https://operation-usage-underpass.ngrok-free.dev', // Atau ganti dengan domain ngrok Vite jika Anda membuat 2 tunnel ngrok
        },
    },
});
