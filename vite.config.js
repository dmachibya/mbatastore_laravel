import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import browserslist from 'browserslist';
import {browserslistToTargets} from 'lightningcss';

export default defineConfig({
    plugins: [
        laravel({
            input   : [
                'public/css/app.css',
         ],
            refresh : true,
        }),
    ],
    css: {
        transformer: 'lightningcss',
        lightningcss: {
          targets: browserslistToTargets(browserslist('>= 0.25%'))
        }
      },
    build : {
        cssMinify: 'lightningcss',
        sourcemap : true,
    },
});