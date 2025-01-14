import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import components from "unplugin-vue-components/vite"
import {AntDesignVueResolver} from "unplugin-vue-components/resolvers"

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        components({
            resolvers: [
                AntDesignVueResolver({
                    importStyle: false
                })
            ]
        })
    ],
});
