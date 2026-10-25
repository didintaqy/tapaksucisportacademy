import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { imagetools } from 'vite-imagetools';
import { beasties } from 'vite-plugin-beasties';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/custom.css',
                'resources/js/custom.js',
                'resources/js/images.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        imagetools({
            // Default directives for all images imported via Vite
            defaultDirectives: (url) => {
                // Generate responsive widths and modern formats
                return new URLSearchParams({
                    w: '400,800,1200',
                    format: 'webp,avif',
                    as: 'picture',
                }).toString();
            },
        }),
        // Inline critical CSS for faster LCP
        beasties({
            // default options are fine; you can customise if needed
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
