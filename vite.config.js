import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        // `ddev npm run dev`: serve Vite from the DDEV container with HMR over the project hostname.
        ...(process.env.IS_DDEV_PROJECT ? {
            strictPort: true,
            host: true,
            hmr: {
                host: process.env.DDEV_HOSTNAME.split(',')[0],
                protocol: 'wss',
            },
        } : {}),
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/underlord.css', 'resources/js/underlord.js', 'resources/css/filament/admin/theme.css'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
});
