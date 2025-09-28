import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { ZiggyVue } from 'ziggy-js';
import PrimeVue from 'primevue/config';
import prettier from "prettier";
import Tag from 'primevue/tag';
import Tooltip from 'primevue/tooltip';
import ToastService from 'primevue/toastservice';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const appUrl = import.meta.env.VITE_APP_URL || 'http://localhost:8000';
createServer(
  (page) =>
    createInertiaApp({
      page,
      render: async (component) => {
        const html = await renderToString(component);
        return prettier.format(html, { parser: "html" }); // khusus dev biar hasil render to string rapih
        // return html; // default
      },
      title: (title) => (title ? `${title} - ${appName}` : appName),
      resolve: (name) => {

        if (name.startsWith('Main/')) {
          return resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob('./pages/Main/**/*.vue') // only turn page from Main folder to SSR

          )
        }
        throw new Error(`Page not support SSR: ${name}`);
      },
      setup: ({ App, props, plugin }) =>
        createSSRApp({ render: () => h(App, props) })
          .use(plugin)
          .use(ZiggyVue, {
            ...page.props.ziggy,
            location: new URL(page.props.ziggy.location),
          })
          .use(ToastService)
          .component('Tag', Tag)
          .directive('tooltip', Tooltip)
          .use(PrimeVue),
    }),
  { cluster: true }
);
