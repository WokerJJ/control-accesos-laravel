import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

import { URL } from 'node:url';

// Accept both full URL (https://techcodex.co/control-accesos) and path-only (/control-accesos)
let base = '/';
if (process.env.ASSET_URL) {
    try {
        base = new URL(process.env.ASSET_URL).pathname;
    } catch {
        // Not a full URL – treat as a path
        base = process.env.ASSET_URL;
    }
    // Ensure leading slash and trailing slash
    if (!base.startsWith('/')) base = '/' + base;
    if (!base.endsWith('/')) base += '/';
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    base,
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
