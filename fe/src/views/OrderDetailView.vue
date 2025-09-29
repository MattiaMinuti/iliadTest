<template>
  <div>
    <v-row class="mb-4">
      <v-col>
        <v-btn
          variant="text"
          prepend-icon="$arrowLeft"
          @click="$router.go(-1)"
        >
          {{ $t('orderDetail.backToOrders') }}
        </v-btn>
      </v-col>
    </v-row>

    <div
      v-if="loading"
      class="text-center py-8"
    >
      <v-progress-circular
        indeterminate
        color="primary"
        size="64"
      />
    </div>

    <div v-else-if="order">
      <!-- Order Header -->
      <v-card
        class="mb-4"
        elevation="1"
      >
        <v-card-text>
          <v-row>
            <v-col
              cols="12"
              md="8"
            >
              <h1 class="text-h4 font-weight-bold mb-2">
                {{ order.name }}
              </h1>
              <p
                v-if="order.description"
                class="text-subtitle-1 text-medium-emphasis"
              >
                {{ order.description }}
              </p>
            </v-col>
            <v-col
              cols="12"
              md="4"
              class="text-md-right"
            >
              <v-chip
                :color="getStatusColor(order.status)"
                size="large"
                variant="tonal"
                class="mb-2"
              >
                {{ order.status }}
              </v-chip>
              <div class="text-h5 font-weight-bold">
                ${{ parseFloat(order.total_amount).toFixed(2) }}
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Order Information -->
      <v-row class="mb-4">
        <v-col
          cols="12"
          md="6"
        >
          <v-card elevation="1">
            <v-card-title>
              {{ $t('orderDetail.orderInformation') }}
            </v-card-title>
            <v-card-text>
              <v-list lines="two">
                <v-list-item>
                  <template #prepend>
                    <v-icon>$calendar</v-icon>
                  </template>
                  <v-list-item-title>
                    {{ $t('orderDetail.orderDate') }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ formatDate(order.order_date) }}
                  </v-list-item-subtitle>
                </v-list-item>

                <v-list-item>
                  <template #prepend>
                    <v-icon>$flag</v-icon>
                  </template>
                  <v-list-item-title>
                    {{ $t('orders.status') }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    <v-chip
                      :color="getStatusColor(order.status)"
                      size="small"
                      variant="tonal"
                    >
                      {{ $t(`status.${order.status}`) }}
                    </v-chip>
                  </v-list-item-subtitle>
                </v-list-item>

                <v-list-item>
                  <template #prepend>
                    <v-icon>$currencyUsd</v-icon>
                  </template>
                  <v-list-item-title>
                    {{ $t('orderDetail.totalAmount') }}
                  </v-list-item-title>
                  <v-list-item-subtitle class="text-h6 font-weight-bold">
                    ${{ parseFloat(order.total_amount).toFixed(2) }}
                  </v-list-item-subtitle>
                </v-list-item>

                <v-list-item>
                  <template #prepend>
                    <v-icon>$packageVariant</v-icon>
                  </template>
                  <v-list-item-title>
                    {{ $t('orderDetail.totalItems') }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{
                      order.products?.reduce(
                        (sum, p) => sum + p.pivot.quantity,
                        0
                      ) || 0
                    }}
                    {{ $t('orderDetail.items') }}
                  </v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col
          cols="12"
          md="6"
        >
          <v-card elevation="1">
            <v-card-title>{{ $t('orderDetail.actions') }}</v-card-title>
            <v-card-text>
              <v-btn
                color="primary"
                variant="outlined"
                prepend-icon="$pencil"
                class="mb-2 mr-2"
                block
                @click="editOrder"
              >
                {{ $t('orderDetail.editOrder') }}
              </v-btn>

              <v-btn
                color="error"
                variant="outlined"
                prepend-icon="$delete"
                block
                @click="deleteOrder"
              >
                {{ $t('orderDetail.deleteOrder') }}
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Order Products -->
      <v-card elevation="1">
        <v-card-title>
          <v-icon class="mr-2">
            $packageVariant
          </v-icon>
          {{ $t('orderDetail.orderProducts') }}
        </v-card-title>

        <v-card-text v-if="!order.products || order.products.length === 0">
          <v-alert
            type="info"
            variant="tonal"
            class="mb-0"
          >
            {{ $t('orderDetail.noProducts') }}
          </v-alert>
        </v-card-text>

        <v-table v-else>
          <thead>
            <tr>
              <th>{{ $t('orderDetail.productName') }}</th>
              <th>{{ $t('orderDetail.sku') }}</th>
              <th class="text-right">
                {{ $t('orderDetail.unitPrice') }}
              </th>
              <th class="text-right">
                {{ $t('orderDetail.quantity') }}
              </th>
              <th class="text-right">
                {{ $t('orderDetail.totalPrice') }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="product in order.products"
              :key="product.id"
            >
              <td>
                <div class="font-weight-medium">
                  {{ product.name }}
                </div>
                <div
                  v-if="product.description"
                  class="text-caption text-medium-emphasis"
                >
                  {{ product.description }}
                </div>
              </td>
              <td>
                <v-chip
                  size="small"
                  variant="outlined"
                >
                  {{ product.sku }}
                </v-chip>
              </td>
              <td class="text-right">
                ${{ parseFloat(product.pivot.unit_price).toFixed(2) }}
              </td>
              <td class="text-right">
                <v-chip
                  size="small"
                  color="primary"
                  variant="tonal"
                >
                  {{ product.pivot.quantity }}
                </v-chip>
              </td>
              <td class="text-right font-weight-bold">
                ${{ parseFloat(product.pivot.total_price).toFixed(2) }}
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="font-weight-bold">
              <td
                colspan="4"
                class="text-right"
              >
                {{ $t('orders.total') }}:
              </td>
              <td class="text-right text-h6">
                ${{ parseFloat(order.total_amount).toFixed(2) }}
              </td>
            </tr>
          </tfoot>
        </v-table>
      </v-card>
    </div>

    <div
      v-else
      class="text-center py-8"
    >
      <v-icon
        size="64"
        color="grey"
      >
        mdi-alert-circle
      </v-icon>
      <h2 class="text-h5 mt-4">
        Order Not Found
      </h2>
      <p class="text-subtitle-1 text-medium-emphasis">
        The requested order could not be found.
      </p>
    </div>

    <!-- Edit Order Dialog -->
    <OrderDialog
      v-model="orderDialog.show"
      :order="orderDialog.order"
      :mode="orderDialog.mode"
      @saved="onOrderSaved"
    />

    <!-- Delete Confirmation Dialog -->
    <v-dialog
      v-model="deleteDialog.show"
      max-width="400"
    >
      <v-card>
        <v-card-title>{{ $t('confirmations.confirmDelete') }}</v-card-title>
        <v-card-text>
          {{ $t('confirmations.deleteOrder', { name: order?.name }) }}
          {{ $t('confirmations.deleteWarning') }}
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            variant="text"
            @click="deleteDialog.show = false"
          >
            {{ $t('common.cancel') }}
          </v-btn>
          <v-btn
            color="error"
            variant="text"
            :loading="deleteDialog.loading"
            @click="confirmDelete"
          >
            {{ $t('common.delete') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { orderService } from '@/services/orders';
import { useNotificationStore } from '@/stores/notification';
import { showApiError } from '@/utils/errorHandler';
import OrderDialog from '@/components/OrderDialog.vue';

export default {
  name: 'OrderDetailView',
  components: {
    OrderDialog,
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const notificationStore = useNotificationStore();
    const { t } = useI18n();

    const loading = ref(true);
    const order = ref(null);

    const orderDialog = reactive({
      show: false,
      order: null,
      mode: 'edit',
    });

    const deleteDialog = reactive({
      show: false,
      loading: false,
    });

    const loadOrder = async () => {
      loading.value = true;
      try {
        const response = await orderService.getOrder(route.params.id);
        order.value = response.data;
      } catch (error) {
        console.error('Error loading order:', error);
        showApiError(notificationStore, error, t, 'messages.loadOrdersFailed');
      } finally {
        loading.value = false;
      }
    };

    const formatDate = date => {
      return new Date(date).toLocaleDateString();
    };

    const getStatusColor = status => {
      const colors = {
        pending: 'orange',
        processing: 'blue',
        completed: 'success',
        cancelled: 'error',
      };
      return colors[status] || 'grey';
    };

    const editOrder = () => {
      // Ensure we have all the necessary data for editing
      orderDialog.order = {
        ...order.value,
        // Ensure products array is properly structured
        products: order.value.products || [],
      };
      orderDialog.mode = 'edit';
      orderDialog.show = true;
    };

    const deleteOrder = () => {
      deleteDialog.show = true;
    };

    const confirmDelete = async () => {
      deleteDialog.loading = true;
      try {
        await orderService.deleteOrder(order.value.id);
        notificationStore.showSuccess(t('messages.orderDeleted'));
        router.push('/');
      } catch (error) {
        console.error('Error deleting order:', error);
        showApiError(notificationStore, error, t, 'messages.deleteOrderFailed');
      } finally {
        deleteDialog.loading = false;
        deleteDialog.show = false;
      }
    };

    const onOrderSaved = () => {
      orderDialog.show = false;
      loadOrder();
      notificationStore.showSuccess(t('messages.orderUpdated'));
    };

    onMounted(() => {
      loadOrder();
    });

    return {
      loading,
      order,
      orderDialog,
      deleteDialog,
      formatDate,
      getStatusColor,
      editOrder,
      deleteOrder,
      confirmDelete,
      onOrderSaved,
    };
  },
};
</script>

<style scoped>
.v-card {
  border-radius: 8px;
}

.v-table th {
  font-weight: 600;
}

.v-table tfoot td {
  border-top: 2px solid rgb(var(--v-theme-primary));
}
</style>
