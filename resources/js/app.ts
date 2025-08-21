import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import Pusher from 'pusher-js';

declare global {
  interface Window {
    Pusher: typeof Pusher;
    Echo: any;
  }
}

// Enable Pusher debugging
Pusher.logToConsole = process.env.NODE_ENV === 'development';

window.Pusher = Pusher;

// const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
// const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap2';
const pusherKey = '30e66a7ce4a62122a6ed';
const pusherCluster = 'ap2';

if (pusherKey && pusherKey === '30e66a7ce4a62122a6ed') {
  import('laravel-echo').then(({ default: Echo }) => {
    const echoConfig: any = {
      broadcaster: 'pusher',
      key: pusherKey,
      cluster: pusherCluster,
      forceTLS: true,
      enabledTransports: ['wss'], 
      disableStats: true,
    };

    const pusherInstance = new Pusher(pusherKey, {
      cluster: pusherCluster,
      forceTLS: true,
      enabledTransports: ['wss'],
    });

    pusherInstance.connection.bind('error', (error: any) => {
      console.error('Pusher connection error details:', error);
      console.error('Error type:', error?.type);
      console.error('Error message:', error?.message);
    });

    pusherInstance.connection.bind('state_change', (states: any) => {
      console.log('Pusher state changed from', states.previous, 'to', states.current);
    });

    pusherInstance.connection.bind('connected', () => {
      console.log('Pusher connected successfully!');
    });

    window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    forceTLS: true,
    enabledTransports: ['wss', 'xhr_streaming', 'xhr_polling'],
    disabledTransports: ['ws', 'sockjs'],
    });

    console.log('Pusher initialized with cluster:', pusherCluster);

  }).catch(error => {
    console.error('Failed to load Laravel Echo:', error);
  });
} else {
  console.warn('Pusher app key not found or is default. Real-time features disabled.');
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue);
    
    app.mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

initializeTheme();