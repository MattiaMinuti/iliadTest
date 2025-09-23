import api from './api'

export const orderService = {
  // Get all orders with optional filters
  async getOrders(params = {}) {
    const response = await api.get('/orders', { params })
    return response.data
  },

  // Get single order by ID
  async getOrder(id) {
    const response = await api.get(`/orders/${id}`)
    return response.data
  },

  // Create new order
  async createOrder(orderData) {
    const response = await api.post('/orders', orderData)
    return response.data
  },

  // Update existing order
  async updateOrder(id, orderData) {
    const response = await api.put(`/orders/${id}`, orderData)
    return response.data
  },

  // Delete order
  async deleteOrder(id) {
    const response = await api.delete(`/orders/${id}`)
    return response.data
  }
}
