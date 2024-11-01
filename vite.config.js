import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/admin/products-preview.js',
                'resources/js/admin/images-actions.js',
                'resources/js/admin/attributes.js',
                'resources/js/cart.js',
                'resources/js/payments/paypal.js',
            ],
            refresh: true,
        }),
    ],
});
