import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import 'vuetify/styles'
import { aliases, mdi } from 'vuetify/iconsets/mdi-svg'
import { 
  mdiClipboardList, 
  mdiBrightness7, 
  mdiBrightness4,
  mdiPackageVariant,
  mdiMagnify,
  mdiEye,
  mdiPencil,
  mdiDelete,
  mdiPlus,
  mdiArrowLeft,
  mdiCalendar,
  mdiFlag,
  mdiCurrencyUsd,
  mdiClose,
  mdiCheck,
  mdiAlert,
  mdiAlertCircle,
  mdiInformation,
  mdiCheckCircle,
  mdiCloseCircle,
  mdiLoading,
  mdiChevronDown,
  mdiChevronUp,
  mdiFilterVariant,
  mdiSortAscending,
  mdiSortDescending,
  mdiRefresh,
  mdiCog,
  mdiDotsVertical,
  mdiMenu
} from '@mdi/js'

import App from './App.vue'
import router from './router'
import i18n from './plugins/i18n'

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi',
    aliases: {
      ...aliases,
      clipboardList: mdiClipboardList,
      brightness7: mdiBrightness7,
      brightness4: mdiBrightness4,
      packageVariant: mdiPackageVariant,
      magnify: mdiMagnify,
      eye: mdiEye,
      pencil: mdiPencil,
      delete: mdiDelete,
      plus: mdiPlus,
      arrowLeft: mdiArrowLeft,
      calendar: mdiCalendar,
      flag: mdiFlag,
      currencyUsd: mdiCurrencyUsd,
      close: mdiClose,
      check: mdiCheck,
      alert: mdiAlert,
      alertCircle: mdiAlertCircle,
      information: mdiInformation,
      checkCircle: mdiCheckCircle,
      closeCircle: mdiCloseCircle,
      loading: mdiLoading,
      chevronDown: mdiChevronDown,
      chevronUp: mdiChevronUp,
      filterVariant: mdiFilterVariant,
      sortAscending: mdiSortAscending,
      sortDescending: mdiSortDescending,
      refresh: mdiRefresh,
      cog: mdiCog,
      dotsVertical: mdiDotsVertical,
      menu: mdiMenu,
    },
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#E60012',        // Rosso Iliad
          secondary: '#333333',      // Grigio scuro
          accent: '#FF6900',         // Arancione Iliad
          error: '#E60012',          // Rosso per errori
          info: '#FF6900',           // Arancione per info
          success: '#4CAF50',        // Verde per successo
          warning: '#FF6900',        // Arancione per warning
          background: '#FFFFFF',     // Bianco
          surface: '#F5F5F5',        // Grigio chiaro
        },
      },
      dark: {
        colors: {
          primary: '#FF6900',        // Arancione per tema scuro
          secondary: '#E0E0E0',      // Grigio chiaro
          accent: '#E60012',         // Rosso Iliad
          error: '#FF5252',          // Rosso chiaro per errori
          info: '#FF6900',           // Arancione per info
          success: '#4CAF50',        // Verde per successo
          warning: '#FF9800',        // Arancione chiaro per warning
          background: '#121212',     // Nero/grigio scuro
          surface: '#1E1E1E',        // Grigio scuro per superficie
        },
      },
    },
  },
})

const pinia = createPinia()

createApp(App)
  .use(pinia)
  .use(router)
  .use(vuetify)
  .use(i18n)
  .mount('#app')
