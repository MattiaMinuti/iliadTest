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
          primary: '#1976D2',
          secondary: '#424242',
          accent: '#82B1FF',
          error: '#FF5252',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FFC107',
        },
      },
      dark: {
        colors: {
          primary: '#2196F3',
          secondary: '#424242',
          accent: '#FF4081',
          error: '#FF5252',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FB8C00',
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
  .mount('#app')
