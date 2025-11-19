import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';

// 1. Impor plugin resmi ZiggyVue
// Pastikan path ini benar setelah Anda publish vendor
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'; 
// atau jika Anda tidak mem-publish:
// import { ZiggyVue } from 'ziggy-js/dist/vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            // 2. Gunakan plugin resmi dan teruskan datanya
            .use(ZiggyVue, props.initialPage.props.ziggy) 
            .mount(el);
    },
    progress: {
        color: '#4B5563',
        showSpinner: false,
    },
});
