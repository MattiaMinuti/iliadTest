<template>
  <v-dialog
    :model-value="modelValue"
    max-width="800px"
    @update:model-value="$emit('update:modelValue', $event)"
    @keydown.esc="closeDialog"
  >
    <v-card>
      <v-card-title class="text-h5">
        {{
          mode === 'create'
            ? $t('orderDialog.createTitle')
            : $t('orderDialog.editTitle')
        }}
      </v-card-title>

      <v-form ref="form" @submit.prevent="saveOrder">
        <v-card-text>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="formData.name"
                :label="$t('orderDialog.orderName') + ' *'"
                variant="outlined"
                :rules="[rules.required]"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="formData.order_date"
                :label="$t('orderDialog.orderDate') + ' *'"
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
                :label="$t('orders.status')"
                variant="outlined"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                :model-value="totalAmount"
                :label="$t('orderDialog.totalAmount')"
                variant="outlined"
                readonly
                prepend-inner-icon="$currencyUsd"
              />
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <v-textarea
                v-model="formData.description"
                :label="$t('orderDialog.description')"
                variant="outlined"
                rows="3"
              />
            </v-col>
          </v-row>

          <v-divider class="my-4" />

          <!-- Products Section -->
          <div class="mb-4">
            <div class="d-flex justify-space-between align-center mb-3">
              <h3 class="text-h6">
                {{ $t('orderDialog.orderProducts') }}
              </h3>
              <v-btn color="primary" size="small" @click="addProduct">
                <v-icon start>$plus</v-icon>
                {{ $t('orderDialog.addProduct') }}
              </v-btn>
            </div>

            <v-alert
              v-if="formData.products.length === 0"
              type="info"
              variant="tonal"
              class="mb-4"
            >
              {{ $t('orderDialog.noProducts') }}
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
                      :label="$t('orderDialog.product') + ' *'"
                      variant="outlined"
                      density="compact"
                      :rules="[rules.required]"
                      @update:model-value="updateProductPrice(index)"
                    >
                      <template #item="{ props, item }">
                        <v-list-item v-bind="props">
                          <template #subtitle>
                            {{ $t('orderDetail.sku') }}: {{ item.raw.sku }} |
                            ${{ item.raw.price }} |
                            {{ $t('orderDialog.stock') }}:
                            {{ item.raw.stock_quantity }}
                          </template>
                        </v-list-item>
                      </template>
                    </v-autocomplete>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-text-field
                      v-model.number="productOrder.quantity"
                      :label="$t('orderDetail.quantity') + ' *'"
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
                      :label="$t('orders.total')"
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
          <v-btn variant="text" @click="closeDialog">
            {{ $t('orderDialog.cancel') }}
          </v-btn>
          <v-btn
            color="primary"
            type="submit"
            :loading="saving"
            :disabled="formData.products.length === 0"
          >
            {{
              mode === 'create'
                ? $t('orderDialog.createOrder')
                : $t('orderDialog.updateOrder')
            }}
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script>
import { ref, reactive, computed, watch, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { orderService } from '@/services/orders';
import { productService } from '@/services/products';
import { useNotificationStore } from '@/stores/notification';
import { showApiError } from '@/utils/errorHandler';

export default {
  name: 'OrderDialog',
  props: {
    modelValue: Boolean,
    order: Object,
    mode: {
      type: String,
      default: 'create',
      validator: value => ['create', 'edit'].includes(value),
    },
  },
  emits: ['update:modelValue', 'saved'],
  setup(props, { emit }) {
    const notificationStore = useNotificationStore();
    const { t } = useI18n();
    const form = ref(null);
    const saving = ref(false);
    const availableProducts = ref([]);

    const formData = reactive({
      name: '',
      description: '',
      order_date: new Date().toISOString().split('T')[0],
      status: 'pending',
      products: [],
    });

    const statusOptions = computed(() => [
      { title: t('status.pending'), value: 'pending' },
      { title: t('status.processing'), value: 'processing' },
      { title: t('status.completed'), value: 'completed' },
      { title: t('status.cancelled'), value: 'cancelled' },
    ]);

    const rules = {
      required: value => !!value || t('validation.required'),
      minQuantity: value => value > 0 || t('validation.minQuantity'),
    };

    const totalAmount = computed(() => {
      return formData.products
        .reduce((sum, product) => {
          const productData = availableProducts.value.find(
            p => p.id === product.product_id
          );
          if (productData && product.quantity) {
            return sum + productData.price * product.quantity;
          }
          return sum;
        }, 0)
        .toFixed(2);
    });

    const loadProducts = async () => {
      // Avoid loading if already loaded
      if (availableProducts.value.length > 0) {
        return;
      }

      try {
        const response = await productService.getProducts({ per_page: 100 });
        availableProducts.value = response.data.data;
      } catch (error) {
        console.error('Error loading products:', error);
        showApiError(
          notificationStore,
          error,
          t,
          'messages.loadProductsFailed'
        );
      }
    };

    const addProduct = () => {
      formData.products.push({
        product_id: null,
        quantity: 1,
      });
    };

    const removeProduct = index => {
      formData.products.splice(index, 1);
    };

    const updateProductPrice = () => {
      calculateTotal();
    };

    const calculateTotal = () => {
      // This will trigger the computed totalAmount
    };

    const getProductTotal = index => {
      const product = formData.products[index];
      const productData = availableProducts.value.find(
        p => p.id === product.product_id
      );
      if (productData && product.quantity) {
        return (productData.price * product.quantity).toFixed(2);
      }
      return '0.00';
    };

    const resetForm = () => {
      formData.name = '';
      formData.description = '';
      formData.order_date = new Date().toISOString().split('T')[0];
      formData.status = 'pending';
      formData.products = [];
    };

    const populateForm = () => {
      if (props.order) {
        formData.name = props.order.name || '';
        formData.description = props.order.description || '';

        // Fix date formatting - handle both string and Date objects
        if (props.order.order_date) {
          const date = new Date(props.order.order_date);
          formData.order_date = date.toISOString().split('T')[0];
        } else {
          formData.order_date = new Date().toISOString().split('T')[0];
        }

        formData.status = props.order.status || 'pending';

        // Populate products - ensure we have the products array
        if (props.order.products && Array.isArray(props.order.products)) {
          formData.products = props.order.products.map(product => ({
            product_id: product.id,
            quantity: product.pivot?.quantity || 1,
          }));
        } else {
          formData.products = [];
        }
      }
    };

    const saveOrder = async () => {
      const { valid } = await form.value.validate();
      if (!valid) return;

      if (formData.products.length === 0) {
        notificationStore.showError(t('messages.addAtLeastOneProduct'));
        return;
      }

      saving.value = true;
      try {
        const orderData = {
          name: formData.name,
          description: formData.description,
          order_date: formData.order_date,
          status: formData.status,
          products: formData.products,
        };

        if (props.mode === 'create') {
          await orderService.createOrder(orderData);
          notificationStore.showSuccess(t('messages.orderCreated'));
        } else {
          await orderService.updateOrder(props.order.id, orderData);
          notificationStore.showSuccess(t('messages.orderUpdated'));
        }

        emit('saved');
      } catch (error) {
        console.error('Error saving order:', error);
        showApiError(notificationStore, error, t, 'messages.saveOrderFailed');
      } finally {
        saving.value = false;
      }
    };

    const closeDialog = () => {
      emit('update:modelValue', false);
    };

    watch(
      () => props.modelValue,
      newValue => {
        if (newValue) {
          if (props.mode === 'create') {
            resetForm();
          } else {
            populateForm();
          }
          loadProducts();
        }
      }
    );

    // Watch for order changes to populate form data
    watch(
      () => props.order,
      () => {
        if (props.modelValue && props.mode === 'edit') {
          populateForm();
        }
      },
      { deep: true }
    );

    // Handle ESC key to close dialog
    const handleKeydown = event => {
      if (event.key === 'Escape' && props.modelValue) {
        closeDialog();
      }
    };

    onMounted(() => {
      // Products will be loaded when dialog opens via watch
      // Add global keydown listener for ESC
      document.addEventListener('keydown', handleKeydown);
    });

    onUnmounted(() => {
      // Remove global keydown listener
      document.removeEventListener('keydown', handleKeydown);
    });

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
      closeDialog,
    };
  },
};
</script>

<style scoped>
.v-card {
  border-radius: 8px;
}
</style>
