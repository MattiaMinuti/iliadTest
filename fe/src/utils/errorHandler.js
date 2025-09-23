import { getCurrentLocale } from '@/plugins/i18n'

/**
 * Handle API error messages and translate them if needed
 * @param {Error} error - The error object from API
 * @param {Function} t - The translation function
 * @returns {string} - Translated error message
 */
export function handleApiError(error, t) {
  // Get the error message from the API response
  const apiMessage = error.response?.data?.message || error.message || 'Unknown error'
  
  // Common error patterns that we want to translate
  const errorPatterns = {
    // Insufficient stock errors
    'Insufficient stock for product:': {
      pattern: /Insufficient stock for product:\s*(.+)/i,
      translateKey: 'messages.insufficientStock',
      extractProduct: true
    },
    
    // Validation errors
    'Validation failed': {
      pattern: /Validation failed/i,
      translateKey: 'messages.validationFailed'
    },
    
    // Not found errors
    'Order not found': {
      pattern: /Order not found/i,
      translateKey: 'messages.orderNotFound'
    },
    
    'Product not found': {
      pattern: /Product not found/i,
      translateKey: 'messages.productNotFound'
    },
    
    // Cannot delete errors
    'Cannot delete product that is associated with orders': {
      pattern: /Cannot delete product that is associated with orders/i,
      translateKey: 'messages.cannotDeleteProduct'
    },
    
    // Server errors
    'Internal Server Error': {
      pattern: /Internal Server Error/i,
      translateKey: 'messages.serverError'
    }
  }
  
  // Check each pattern
  for (const [key, config] of Object.entries(errorPatterns)) {
    const match = apiMessage.match(config.pattern)
    if (match) {
      if (config.extractProduct && match[1]) {
        // Extract product name and use it in translation
        const productName = match[1].trim()
        return t(config.translateKey, { product: productName })
      } else {
        return t(config.translateKey)
      }
    }
  }
  
  // If no pattern matches, return the original message
  return apiMessage
}

/**
 * Show error notification with proper translation
 * @param {Object} notificationStore - The notification store
 * @param {Error} error - The error object
 * @param {Function} t - The translation function
 * @param {string} fallbackKey - Fallback translation key if no pattern matches
 */
export function showApiError(notificationStore, error, t, fallbackKey = 'messages.genericError') {
  const translatedMessage = handleApiError(error, t)
  notificationStore.showError(translatedMessage)
}
