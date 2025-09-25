<template>
  <v-menu>
    <template #activator="{ props }">
      <v-btn
        icon
        variant="text"
        v-bind="props"
      >
        <span class="text-h6">{{ currentLocale.flag }}</span>
        <v-tooltip
          activator="parent"
          location="bottom"
        >
          {{ $t('app.changeLanguage') }}
        </v-tooltip>
      </v-btn>
    </template>

    <v-list
      density="compact"
      min-width="160"
    >
      <v-list-item
        v-for="locale in availableLocales"
        :key="locale.code"
        :class="{ 'v-list-item--active': locale.code === currentLocale.code }"
        @click="changeLanguage(locale.code)"
      >
        <template #prepend>
          <span class="mr-3">{{ locale.flag }}</span>
        </template>
        <v-list-item-title>{{ locale.name }}</v-list-item-title>
        <template
          v-if="locale.code === currentLocale.code"
          #append
        >
          <v-icon
            color="primary"
            size="small"
          >
            $check
          </v-icon>
        </template>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  setLocale,
  getAvailableLocales,
} from '@/plugins/i18n';

export default {
  name: 'LanguageSwitcher',
  setup() {
    const { locale } = useI18n();

    const availableLocales = getAvailableLocales();

    const currentLocale = computed(() => {
      return (
        availableLocales.find(l => l.code === locale.value) ||
        availableLocales[0]
      );
    });

    const changeLanguage = newLocale => {
      setLocale(newLocale);
      // Trigger a small notification or feedback
    };

    return {
      availableLocales,
      currentLocale,
      changeLanguage,
    };
  },
};
</script>

<style scoped>
.v-list-item--active {
  background-color: rgba(var(--v-theme-primary), 0.1);
}
</style>
