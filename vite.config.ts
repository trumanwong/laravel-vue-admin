// @ts-ignore
import path from 'path'
// @ts-ignore
import {defineConfig} from 'vite'
// @ts-ignore
import vue from '@vitejs/plugin-vue'
// @ts-ignore
import vueJsx from '@vitejs/plugin-vue-jsx'
// @ts-ignore
import VueSetupExtend from 'vite-plugin-vue-setup-extend-plus'
// @ts-ignore
import AutoImport from 'unplugin-auto-import/vite'

function resolve(dir: any) {
    return path.join(
        __dirname,
        '/resources/js',
        dir
    );
}

export default defineConfig({
    base: '/build/',
    //define global var
    define: {
        //fix "path" module issue
        'process.platform': null,
        'process.version': null,
        'import.meta.env': {}
    },
    plugins: [
        vue(),
        vueJsx(),
        VueSetupExtend(),
        //https://github.com/antfu/unplugin-auto-import/blob/HEAD/src/types.ts
        AutoImport({
            // resolvers: [ElementPlusResolver()],
            imports: [
                'vue',
                'vue-router'
            ],
            eslintrc: {
                enabled: true, // Default `false`
                filepath: './.eslintrc-auto-import.json', // Default `./.eslintrc-auto-import.json`
                globalsPropValue: true // Default `true`, (true | false | 'readonly' | 'readable' | 'writable' | 'writeable')
            },
            dts: true //auto generation auto-imports.d.ts file
        }),
    ],
    resolve: {
        alias: {
            '@': path.join(__dirname, '/resources/js'),
            //remove i18n waring
            'vue-i18n': 'vue-i18n/dist/vue-i18n.cjs.js'
        }
    },
    build: {
        // 在 outDir 中生成 manifest.json
        manifest: true,
        outDir: 'public/build',
        // 消除打包大小超过警告
        chunkSizeWarningLimit: 5000,
        //remote console.log in prod
        terserOptions: {
            //detail to look https://terser.org/docs/api-reference#compress-options
            compress: {
                drop_console: false,
                pure_funcs: ['console.log', 'console.info'],
                drop_debugger: true
            }
        },
        rollupOptions: {
            // 覆盖默认的 .html 入口
            input: 'resources/js/app.js'
        }
    },
    css: {
        postcss: {
            //remove build charset warning
            plugins: [
                {
                    postcssPlugin: 'internal:charset-removal',
                    AtRule: {
                        charset: (atRule) => {
                            if (atRule.name === 'charset') {
                                atRule.remove()
                            }
                        }
                    }
                }
            ]
        },
        preprocessorOptions: {
            //define global scss variable
            scss: {
                additionalData: `@import "@/styles/variables.scss";`
            }
        }
    },
})
