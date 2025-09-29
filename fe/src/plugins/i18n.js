import { createI18n } from 'vue-i18n';
import en from '@/locales/en.js';
import it from '@/locales/it.js';

/**
 * @typedef {Object} I18nInstance
 * @property {Function} t - Translation function
 * @property {Function} tc - Translation choice function
 * @property {Function} te - Translation exists function
 * @property {Function} d - Date formatting function
 * @property {Function} n - Number formatting function
 */

/**
 * Global i18n instance with translation functions
 * @type {I18nInstance}
 */

// Get saved language from localStorage or default to Italian
const savedLocale = localStorage.getItem('locale') || 'it';

const i18n = createI18n({
  legacy: false, // Use Composition API mode
  locale: savedLocale,
  fallbackLocale: 'en',
  globalInjection: true,
  messages: {
    en,
    it,
  },
});

// Function to change language and persist it
export function setLocale(locale) {
  i18n.global.locale.value = locale;
  localStorage.setItem('locale', locale);
  document.documentElement.lang = locale;
}

// Function to get current locale
export function getCurrentLocale() {
  return i18n.global.locale.value;
}

// Function to get available locales
export function getAvailableLocales() {
  return [
    { code: 'it', name: 'Italiano', flag: '🇮🇹' },
    { code: 'en', name: 'English', flag: '🇬🇧' },
  ];
}

export default i18n;
