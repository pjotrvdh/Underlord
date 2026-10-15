import { execSync } from 'node:child_process';
import { defineConfig } from 'vite';

/**
 * The short hash and commit time (Europe/Amsterdam) of HEAD, or null outside a git checkout.
 *
 * @returns {{hash: string, time: string}|null}
 */
function buildVersion() {
    try {
        const [hash, committedAt] = execSync('git log -1 --format="%h %cI"', { encoding: 'utf8' }).trim().split(' ');
        const time = new Intl.DateTimeFormat('sv-SE', {
            timeZone: 'Europe/Amsterdam',
            dateStyle: 'short',
            timeStyle: 'short',
        }).format(new Date(committedAt));

        return { hash, time };
    } catch {
        return null;
    }
}

// `ddev start`: Vite runs inside the DDEV web container and is served through the DDEV router
// on https://<project>.ddev.site (see .ddev/config.yaml), with hot reloading over wss.
const ddevHost = process.env.IS_DDEV_PROJECT ? process.env.DDEV_HOSTNAME?.split(',')[0] : null;

export default defineConfig({
    // Relative paths, so the build also works from a sub-path such as GitHub Pages (/Underlord/).
    base: './',
    // public/assets holds the game's SVGs, so the bundled JS and CSS go to dist/build instead.
    build: {
        assetsDir: 'build',
    },
    server: ddevHost ? {
        host: true,
        port: 5173,
        strictPort: true,
        allowedHosts: [ddevHost],
        hmr: { host: ddevHost, protocol: 'wss', clientPort: 443 },
    } : {},
    define: {
        __BUILD_VERSION__: JSON.stringify(buildVersion()),
    },
});
