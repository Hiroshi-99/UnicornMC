import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/styles.css",
                "resources/css/store.css",
                "resources/css/order.css",
                "resources/js/order.js",
                "resources/js/main.js",
                "resources/js/firefly.js",
                "resources/js/store.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources",
        },
    },
});
