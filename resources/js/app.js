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
import ConfirmationService from 'primevue/confirmationservice';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Calendar from 'primevue/calendar';
import InputSwitch from 'primevue/inputswitch';
import FileUpload from 'primevue/fileupload';
import Tag from 'primevue/tag';
import Badge from 'primevue/badge';
import Card from 'primevue/card';
import ConfirmDialog from 'primevue/confirmdialog';
import Dialog from 'primevue/dialog';
import Chip from 'primevue/chip';
import Checkbox from 'primevue/checkbox';
import Rating from 'primevue/rating';
import MultiSelect from 'primevue/multiselect';
import ToggleButton from 'primevue/togglebutton';
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
            ]
        );
        const key = Object.keys(pages).find(k => k.endsWith(`/${name}.vue`));
        if (!key) {
            throw new Error(`Page not found: ${name}`);
        }
        return pages[key]();
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
        app.use(ConfirmationService);
        app.component('Button', Button);
        app.component('InputText', InputText);
        app.component('InputNumber', InputNumber);
        app.component('Textarea', Textarea);
        app.component('Select', Select);
        app.component('DataTable', DataTable);
        app.component('Column', Column);
        app.component('Calendar', Calendar);
        app.component('InputSwitch', InputSwitch);
        app.component('FileUpload', FileUpload);
        app.component('Tag', Tag);
        app.component('Badge', Badge);
        app.component('Card', Card);
        app.component('ConfirmDialog', ConfirmDialog);
        app.component('Dialog', Dialog);
        app.component('Chip', Chip);
        app.component('Checkbox', Checkbox);
        app.component('Rating', Rating);
        app.component('MultiSelect', MultiSelect);
        app.component('ToggleButton', ToggleButton);
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
