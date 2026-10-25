import { defineConfig } from 'vite';
import path from 'node:path';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { imagetools } from 'vite-imagetools';
import { beasties } from 'vite-plugin-beasties';

export default defineConfig({
    // Template CSS references images as url('../images/x.svg'), but the images
    // live in public/images (served by Laravel directly in dev, emitted into
    // /build/assets in production). Without the alias Vite looks in
    // resources/images, fails to resolve them and leaves the url() untouched,
    // which the browser resolves against /build/assets/ -> 404.
    resolve: {
        alias: [
            {
                find: /^\.\.\/images\//,
                replacement: path.resolve(__dirname, 'public/images') + '/',
            },
        ],
    },
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
    // Assets referenced from CSS are emitted into public/build/assets with a
    // content hash, so point at them through the /build/ prefix Laravel serves
    // (relative '../images/x' would resolve to /build/images/x and 404).
    experimental: {
        renderBuiltUrl(filename, { hostType, type }) {
            if (type === 'asset' && hostType === 'css') {
                return `/build/${filename}`;
            }

            return undefined;
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
