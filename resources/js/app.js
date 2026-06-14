import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            console.warn('Service worker registration failed');
        });
    });
}
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import ToastService from 'primevue/toastservice';
import AuthAtmosphere from './Components/AuthAtmosphere.vue';

// Mount AuthAtmosphere on standalone Blade auth pages
const authRoot = document.getElementById('auth-atmosphere-root');
if (authRoot) {
    const atmosphereApp = createApp(AuthAtmosphere);
    atmosphereApp.mount(authRoot);
}

// Standalone (Blade) pages 3D background
import('./standalone-3d').then(({ initStandalone3D }) => {
    const canvas = document.getElementById('three-canvas');
    if (canvas) {
        initStandalone3D('three-canvas');
    }
});

const appName = import.meta.env.VITE_APP_NAME || 'Orchestrix';

createInertiaApp({
    title: (title) => `${title} — ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob(
            [
                './Pages/**/*.vue',
                '../../app/Modules/AgriVerse/Resources/js/Pages/**/*.vue',
            ],
            { eager: true }
        );
        const key = Object.keys(pages).find(k => k.endsWith(`/${name}.vue`));
        if (!key) {
            throw new Error(`Page not found: ${name}`);
        }
        return pages[key];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(ZiggyVue);
        app.use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    prefix: 'p',
                    darkModeSelector: '.dark-mode',
                    cssLayer: false,
                },
            },
        });
        app.use(ToastService);
        app.mount(el);
    },
    progress: {
        color: '#059669',
        showSpinner: true,
    },
});

let currentUserId = null;

router.on('start', (event) => {
    const page = event.detail.page;
    const userId = page?.props?.auth?.user?.id;
    if (currentUserId !== null && userId !== undefined && userId !== currentUserId) {
        window.location.reload();
    }
    currentUserId = userId;
});

router.on('success', (event) => {
    const page = event.detail.page;
    const userId = page?.props?.auth?.user?.id;
    if (userId !== undefined) {
        currentUserId = userId;
    }
});
