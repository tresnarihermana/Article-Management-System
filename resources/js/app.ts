import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/lara';
import '@primeuix/themes/lara';
import 'primeicons/primeicons.css';
import ToastService from 'primevue/toastservice';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue'
import VueApexCharts from "vue3-apexcharts";

import Tooltip from 'primevue/tooltip';
import autoAnimate from '@formkit/auto-animate';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        app.component("apexchart", VueApexCharts);
        app.directive('tooltip', Tooltip);
        app.directive('auto-animate', autoAnimate);
        app.use(plugin)
            .use(ZiggyVue)
            .use(ToastService)
            .use(autoAnimatePlugin)
            .use(VueApexCharts)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: '.dark' // ini penting biar ikut class dark kita
                    }

                }
            })
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
