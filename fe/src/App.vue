<template>
  <v-app>
    <v-app-bar
      color="primary"
      dark
      app
      elevation="1"
      height="64"
    >
      <v-app-bar-title class="font-weight-bold">
        <v-icon class="mr-2" size="28">$clipboardList</v-icon>
        <span class="text-h5">Order Management</span>
      </v-app-bar-title>
      
      <v-spacer></v-spacer>
      
      <LanguageSwitcher class="mr-2" />
      
      <v-btn
        icon
        variant="text"
        @click="toggleTheme"
      >
        <v-icon>{{ isDark ? '$brightness7' : '$brightness4' }}</v-icon>
        <v-tooltip activator="parent" location="bottom">
          {{ $t('app.toggleTheme') }}
        </v-tooltip>
      </v-btn>
    </v-app-bar>

    <v-navigation-drawer
      v-model="drawer"
      app
      temporary
    >
      <v-list>
        <v-list-item
          v-for="item in menuItems"
          :key="item.title"
          :to="item.route"
          link
        >
          <template v-slot:prepend>
            <v-icon>{{ item.icon }}</v-icon>
          </template>
          <v-list-item-title>{{ $t(item.title) }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container fluid>
        <router-view />
      </v-container>
    </v-main>

    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      :timeout="snackbar.timeout"
      location="top right"
    >
      {{ snackbar.message }}
      <template v-slot:actions>
        <v-btn
          variant="text"
          @click="snackbar.show = false"
        >
          {{ $t('common.close') }}
        </v-btn>
      </template>
    </v-snackbar>
  </v-app>
</template>

<script>
import { ref, computed } from 'vue'
import { useTheme } from 'vuetify'
import { useNotificationStore } from '@/stores/notification'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

export default {
  name: 'App',
  components: {
    LanguageSwitcher
  },
  setup() {
    const theme = useTheme()
    const notificationStore = useNotificationStore()
    
    const drawer = ref(false)
    
    const menuItems = [
      { title: 'nav.orders', icon: '$clipboardList', route: '/' },
      { title: 'nav.products', icon: '$packageVariant', route: '/products' },
    ]
    
    const isDark = computed(() => theme.global.name.value === 'dark')
    
    const toggleTheme = () => {
      theme.global.name.value = theme.global.current.value.dark ? 'light' : 'dark'
    }
    
    const snackbar = computed(() => notificationStore.snackbar)
    
    return {
      drawer,
      menuItems,
      isDark,
      toggleTheme,
      snackbar,
    }
  }
}
</script>

<style scoped>
.v-app-bar-title {
  font-weight: 700;
  letter-spacing: -0.5px;
}

.v-app-bar {
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
</style>

<style>
/* Stili globali per il tema Iliad */
.v-btn--variant-elevated {
  box-shadow: 0 2px 8px rgba(230, 0, 18, 0.2) !important;
}

.v-btn--variant-elevated:hover {
  box-shadow: 0 4px 12px rgba(230, 0, 18, 0.3) !important;
}

.v-card {
  border-radius: 8px !important;
}

.v-chip {
  font-weight: 500;
}

.v-data-table .v-data-table-header {
  background-color: rgba(245, 245, 245, 0.8);
}
</style>
