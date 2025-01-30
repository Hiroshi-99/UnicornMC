import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/styles.css",
                "resources/css/store.css",
                "resources/css/order.css",
                "resources/js/main.js",
                "resources/js/firefly.js",
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
