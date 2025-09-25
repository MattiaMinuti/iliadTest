import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNotificationStore = defineStore('notification', () => {
  const snackbar = ref({
    show: false,
    message: '',
    color: 'success',
    timeout: 4000,
  });

  const showSuccess = message => {
    snackbar.value = {
      show: true,
      message,
      color: 'success',
      timeout: 4000,
    };
  };

  const showError = message => {
    snackbar.value = {
      show: true,
      message,
      color: 'error',
      timeout: 6000,
    };
  };

  const showInfo = message => {
    snackbar.value = {
      show: true,
      message,
      color: 'info',
      timeout: 4000,
    };
  };

  const showWarning = message => {
    snackbar.value = {
      show: true,
      message,
      color: 'warning',
      timeout: 5000,
    };
  };

  return {
    snackbar,
    showSuccess,
    showError,
    showInfo,
    showWarning,
  };
});
