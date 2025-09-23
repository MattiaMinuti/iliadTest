<template>
  <v-app>
    <v-app-bar
      color="primary"
      dark
      app
      elevation="2"
    >
      <v-app-bar-title>
        <v-icon class="mr-2">$clipboardList</v-icon>
        Order Management System
      </v-app-bar-title>
      
      <v-spacer></v-spacer>
      
      <v-btn
        icon
        @click="toggleTheme"
      >
        <v-icon>{{ isDark ? '$brightness7' : '$brightness4' }}</v-icon>
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
          <v-list-item-title>{{ item.title }}</v-list-item-title>
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
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-app>
</template>

<script>
import { ref, computed } from 'vue'
import { useTheme } from 'vuetify'
import { useNotificationStore } from '@/stores/notification'

export default {
  name: 'App',
  setup() {
    const theme = useTheme()
    const notificationStore = useNotificationStore()
    
    const drawer = ref(false)
    
    const menuItems = [
      { title: 'Orders', icon: '$clipboardList', route: '/' },
      { title: 'Products', icon: '$packageVariant', route: '/products' },
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
  font-weight: 500;
}
</style>
