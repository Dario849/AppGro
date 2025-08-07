// vite.config.js
import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';
import usePHP from 'vite-plugin-php';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import { ViteEjsPlugin } from 'vite-plugin-ejs';
import { imagetools } from 'vite-imagetools';
import { existsSync } from 'node:fs';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ command }) => {
	const publicBasePath = '/.'; // Change if deploying under a nested public path. Needs to end with a /. See https://vitejs.dev/guide/build.html#public-base-path

	const base = command === 'serve' ? '/' : publicBasePath;
	const BASE = base.substring(0, base.length - 1);

	return {
		base,
		plugins: [
			imagetools(),
			usePHP({
				entry: [
					'index.php',
					'pages/**/*.php',
					'partials/**/*.php',
				],
				rewriteUrl(requestUrl) {
					const filePath = fileURLToPath(
						new URL('.' + requestUrl.pathname, import.meta.url)
					);
					const publicFilePath = fileURLToPath(
						new URL('./pages' + requestUrl.pathname, import.meta.url)
					);

					if (
						!requestUrl.pathname.includes('.php') &&
						(existsSync(filePath) || existsSync(publicFilePath))
					) {
						return undefined;
					}

					requestUrl.pathname = 'index.php';
					return requestUrl;
				},
			}),
			ViteEjsPlugin({ BASE }),
			viteStaticCopy({
				targets: [
					{ src: 'public', dest: '' },
					{ src: 'js', dest: '' },
					{ src: 'system', dest: '' },
					{ src: 'configs', dest: '', overwrite: false },
					{ src: 'vendor', dest: '' },
				],
				silent: command === 'serve',
			}),
			tailwindcss(),
		],
		define: {
			'BASE': JSON.stringify(BASE),
			'import.meta.env.BASE': JSON.stringify(BASE),
		},
		resolve: {
			alias: {
				'~/': fileURLToPath(new URL('./src/', import.meta.url)),
			},
		},
		publicDir: command === 'build' ? 'raw' : 'public',
		css: {
			preprocessorOptions: {
				scss: {
					api: 'modern-compiler',
				},
			},
		},
		server: {
			port: 8080,
		},
		build: {
			outDir: 'dist',
			emptyOutDir: true,
			assetsDir: 'pages',
		},
	};
});
