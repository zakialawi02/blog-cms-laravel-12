import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/ckeditor.css",
                "resources/js/app.js",
                "resources/js/app-dashboard.js",
                "resources/js/ckeditor.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@assets": "/resources/assets",
        },
    },
});
