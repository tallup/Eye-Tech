import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { Toaster, toast } from 'vue-sonner'
import '../css/app.css'

createInertiaApp({
  title: (title) => `${title} — EyeTech`,
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: false })
    return pages[`./Pages/${name}.vue`]() as Promise<any>
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)
    app.component('Toaster', Toaster)
    app.mount(el)
  },
  progress: { color: 'hsl(0 72% 51%)' },
})

router.on('error', () => {
  toast.error('Connection lost — please retry.')
})
