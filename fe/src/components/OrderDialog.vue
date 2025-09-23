<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    max-width="800px"
    persistent
  >
    <v-card>
      <v-card-title class="text-h5">
        {{ mode === 'create' ? 'Create New Order' : 'Edit Order' }}
      </v-card-title>

      <v-form ref="form" @submit.prevent="saveOrder">
        <v-card-text>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="formData.name"
                label="Order Name *"
                variant="outlined"
                :rules="[rules.required]"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="formData.order_date"
                label="Order Date *"
                type="date"
                variant="outlined"
                :rules="[rules.required]"
                required
              />
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="6">
              <v-select
                v-model="formData.status"
                :items="statusOptions"
                label="Status"
                variant="outlined"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                :model-value="totalAmount"
                label="Total Amount"
                variant="outlined"
                readonly
                prepend-inner-icon="mdi-currency-usd"
              />
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <v-textarea
                v-model="formData.description"
                label="Description"
                variant="outlined"
                rows="3"
              />
            </v-col>
          </v-row>

          <v-divider class="my-4" />

          <!-- Products Section -->
          <div class="mb-4">
            <div class="d-flex justify-space-between align-center mb-3">
              <h3 class="text-h6">Order Products</h3>
              <v-btn
                color="primary"
                size="small"
                @click="addProduct"
              >
                <v-icon start>$plus</v-icon>
                Add Product
              </v-btn>
            </div>

            <v-alert
              v-if="formData.products.length === 0"
              type="info"
              variant="tonal"
              class="mb-4"
            >
              No products added to this order yet. Click "Add Product" to get started.
            </v-alert>

            <v-card
              v-for="(productOrder, index) in formData.products"
              :key="index"
              variant="outlined"
              class="mb-3"
            >
              <v-card-text>
                <v-row align="center">
                  <v-col cols="12" md="5">
                    <v-autocomplete
                      v-model="productOrder.product_id"
                      :items="availableProducts"
                      item-title="name"
                      item-value="id"
                      label="Product *"
                      variant="outlined"
                      density="compact"
                      :rules="[rules.required]"
                      @update:model-value="updateProductPrice(index)"
                    >
                      <template v-slot:item="{ props, item }">
                        <v-list-item v-bind="props">
                          <template v-slot:subtitle>
                            SKU: {{ item.raw.sku }} | ${{ item.raw.price }} | Stock: {{ item.raw.stock_quantity }}
                          </template>
                        </v-list-item>
                      </template>
                    </v-autocomplete>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-text-field
                      v-model.number="productOrder.quantity"
                      label="Quantity *"
                      type="number"
                      variant="outlined"
                      density="compact"
                      min="1"
                      :rules="[rules.required, rules.minQuantity]"
                      @input="calculateTotal"
                    />
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-text-field
                      :model-value="getProductTotal(index)"
                      label="Total"
                      variant="outlined"
                      density="compact"
                      readonly
                      prepend-inner-icon="$currencyUsd"
                    />
                  </v-col>
                  <v-col cols="12" md="1">
                    <v-btn
                      icon
                      size="small"
                      color="error"
                      variant="text"
                      @click="removeProduct(index)"
                    >
                      <v-icon>$delete</v-icon>
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            variant="text"
            @click="closeDialog"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            type="submit"
            :loading="saving"
            :disabled="formData.products.length === 0"
          >
            {{ mode === 'create' ? 'Create Order' : 'Update Order' }}
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { orderService } from '@/services/orders'
import { productService } from '@/services/products'
import { useNotificationStore } from '@/stores/notification'

export default {
  name: 'OrderDialog',
  props: {
    modelValue: Boolean,
    order: Object,
    mode: {
      type: String,
      default: 'create',
      validator: (value) => ['create', 'edit'].includes(value)
    }
  },
  emits: ['update:modelValue', 'saved'],
  setup(props, { emit }) {
    const notificationStore = useNotificationStore()
    const form = ref(null)
    const saving = ref(false)
    const availableProducts = ref([])

    const formData = reactive({
      name: '',
      description: '',
      order_date: new Date().toISOString().split('T')[0],
      status: 'pending',
      products: []
    })

    const statusOptions = [
      { title: 'Pending', value: 'pending' },
      { title: 'Processing', value: 'processing' },
      { title: 'Completed', value: 'completed' },
      { title: 'Cancelled', value: 'cancelled' }
    ]

    const rules = {
      required: (value) => !!value || 'This field is required',
      minQuantity: (value) => value > 0 || 'Quantity must be greater than 0'
    }

    const totalAmount = computed(() => {
      return formData.products.reduce((sum, product) => {
        const productData = availableProducts.value.find(p => p.id === product.product_id)
        if (productData && product.quantity) {
          return sum + (productData.price * product.quantity)
        }
        return sum
      }, 0).toFixed(2)
    })

    const loadProducts = async () => {
      try {
        const response = await productService.getProducts({ per_page: 1000 })
        availableProducts.value = response.data.data
      } catch (error) {
        console.error('Error loading products:', error)
        notificationStore.showError('Failed to load products')
      }
    }

    const addProduct = () => {
      formData.products.push({
        product_id: null,
        quantity: 1
      })
    }

    const removeProduct = (index) => {
      formData.products.splice(index, 1)
    }

    const updateProductPrice = (index) => {
      calculateTotal()
    }

    const calculateTotal = () => {
      // This will trigger the computed totalAmount
    }

    const getProductTotal = (index) => {
      const product = formData.products[index]
      const productData = availableProducts.value.find(p => p.id === product.product_id)
      if (productData && product.quantity) {
        return (productData.price * product.quantity).toFixed(2)
      }
      return '0.00'
    }

    const resetForm = () => {
      formData.name = ''
      formData.description = ''
      formData.order_date = new Date().toISOString().split('T')[0]
      formData.status = 'pending'
      formData.products = []
    }

    const populateForm = () => {
      if (props.order) {
        formData.name = props.order.name || ''
        formData.description = props.order.description || ''
        formData.order_date = props.order.order_date || new Date().toISOString().split('T')[0]
        formData.status = props.order.status || 'pending'
        
        // Populate products
        formData.products = props.order.products?.map(product => ({
          product_id: product.id,
          quantity: product.pivot.quantity
        })) || []
      }
    }

    const saveOrder = async () => {
      const { valid } = await form.value.validate()
      if (!valid) return

      if (formData.products.length === 0) {
        notificationStore.showError('Please add at least one product to the order')
        return
      }

      saving.value = true
      try {
        const orderData = {
          name: formData.name,
          description: formData.description,
          order_date: formData.order_date,
          status: formData.status,
          products: formData.products
        }

        if (props.mode === 'create') {
          await orderService.createOrder(orderData)
          notificationStore.showSuccess('Order created successfully')
        } else {
          await orderService.updateOrder(props.order.id, orderData)
          notificationStore.showSuccess('Order updated successfully')
        }

        emit('saved')
      } catch (error) {
        console.error('Error saving order:', error)
        const message = error.response?.data?.message || 'Failed to save order'
        notificationStore.showError(message)
      } finally {
        saving.value = false
      }
    }

    const closeDialog = () => {
      emit('update:modelValue', false)
    }

    watch(() => props.modelValue, (newValue) => {
      if (newValue) {
        if (props.mode === 'create') {
          resetForm()
        } else {
          populateForm()
        }
        loadProducts()
      }
    })

    onMounted(() => {
      loadProducts()
    })

    return {
      form,
      saving,
      availableProducts,
      formData,
      statusOptions,
      rules,
      totalAmount,
      addProduct,
      removeProduct,
      updateProductPrice,
      calculateTotal,
      getProductTotal,
      saveOrder,
      closeDialog
    }
  }
}
</script>

<style scoped>
.v-card {
  border-radius: 8px;
}
</style>
