<template>
  <div>
    <v-row class="mb-4">
      <v-col>
        <h1 class="text-h4 font-weight-bold mb-2">
          {{ $t('orders.title') }}
        </h1>
        <p class="text-subtitle-1 text-medium-emphasis">
          {{ $t('orders.subtitle') }}
        </p>
      </v-col>
    </v-row>

    <!-- Filters and Search -->
    <v-card
      class="mb-4"
      elevation="1"
    >
      <v-card-text>
        <v-row>
          <v-col
            cols="12"
            md="4"
          >
            <v-text-field
              v-model="filters.search"
              :label="$t('orders.searchPlaceholder')"
              prepend-inner-icon="$magnify"
              variant="outlined"
              density="compact"
              clearable
              @input="debouncedSearch"
            />
          </v-col>
          <v-col
            cols="12"
            md="2"
          >
            <v-text-field
              v-model="filters.startDate"
              :label="$t('orders.startDate')"
              type="date"
              variant="outlined"
              density="compact"
              @change="loadOrders"
            />
          </v-col>
          <v-col
            cols="12"
            md="2"
          >
            <v-text-field
              v-model="filters.endDate"
              :label="$t('orders.endDate')"
              type="date"
              variant="outlined"
              density="compact"
              @change="loadOrders"
            />
          </v-col>
          <v-col
            cols="12"
            md="2"
          >
            <v-select
              v-model="filters.status"
              :items="statusOptions"
              :label="$t('orders.status')"
              variant="outlined"
              density="compact"
              clearable
              @update:model-value="loadOrders"
            />
          </v-col>
          <v-col
            cols="12"
            md="2"
          >
            <v-btn
              color="primary"
              variant="elevated"
              block
              @click="openCreateDialog"
            >
              <v-icon start>
                $plus
              </v-icon>
              {{ $t('orders.newOrder') }}
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Orders Table -->
    <v-card elevation="1">
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :headers="headers"
        :items="orders"
        :items-length="totalItems"
        :loading="loading"
        :items-per-page-options="[5, 10, 15, 25, 50]"
        item-value="id"
        @update:options="loadOrders"
      >
        <template #item.order_date="{ item }">
          {{ formatDate(item.order_date) }}
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            size="small"
            variant="tonal"
          >
            {{ item.status }}
          </v-chip>
        </template>

        <template #item.total_amount="{ item }">
          ${{ parseFloat(item.total_amount).toFixed(2) }}
        </template>

        <template #item.actions="{ item }">
          <v-btn
            icon
            size="small"
            variant="text"
            @click="viewOrder(item.id)"
          >
            <v-icon>$eye</v-icon>
            <v-tooltip
              activator="parent"
              location="top"
            >
              {{ $t('orders.viewDetails') }}
            </v-tooltip>
          </v-btn>
          <v-btn
            icon
            size="small"
            variant="text"
            @click="editOrder(item)"
          >
            <v-icon>$pencil</v-icon>
            <v-tooltip
              activator="parent"
              location="top"
            >
              {{ $t('orders.edit') }}
            </v-tooltip>
          </v-btn>
          <v-btn
            icon
            size="small"
            variant="text"
            color="error"
            @click="deleteOrder(item)"
          >
            <v-icon>$delete</v-icon>
            <v-tooltip
              activator="parent"
              location="top"
            >
              {{ $t('orders.delete') }}
            </v-tooltip>
          </v-btn>
        </template>
      </v-data-table-server>
    </v-card>

    <!-- Create/Edit Order Dialog -->
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
          {{
            $t('confirmations.deleteOrder', { name: deleteDialog.order?.name })
          }}
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
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { orderService } from '@/services/orders';
import { useNotificationStore } from '@/stores/notification';
import { showApiError } from '@/utils/errorHandler';
import OrderDialog from '@/components/OrderDialog.vue';

export default {
  name: 'OrdersView',
  components: {
    OrderDialog,
  },
  setup() {
    const router = useRouter();
    const notificationStore = useNotificationStore();
    const { t } = useI18n();

    const loading = ref(false);
    const orders = ref([]);
    const totalItems = ref(0);
    const page = ref(1);
    const itemsPerPage = ref(15);

    const filters = reactive({
      search: '',
      startDate: '',
      endDate: '',
      status: null,
    });

    const orderDialog = reactive({
      show: false,
      order: null,
      mode: 'create',
      loading: false,
    });

    const deleteDialog = reactive({
      show: false,
      order: null,
      loading: false,
    });

    const headers = computed(() => [
      { title: t('orders.orderName'), key: 'name', sortable: true },
      { title: t('orders.date'), key: 'order_date', sortable: true },
      { title: t('orders.status'), key: 'status', sortable: true },
      { title: t('orders.total'), key: 'total_amount', sortable: true },
      {
        title: t('orders.actions'),
        key: 'actions',
        sortable: false,
        width: '120px',
      },
    ]);

    const statusOptions = computed(() => [
      { title: t('status.pending'), value: 'pending' },
      { title: t('status.processing'), value: 'processing' },
      { title: t('status.completed'), value: 'completed' },
      { title: t('status.cancelled'), value: 'cancelled' },
    ]);

    let searchTimeout = null;
    const debouncedSearch = () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        loadOrders();
      }, 500);
    };

    const loadOrders = async () => {
      loading.value = true;
      try {
        const params = {
          page: page.value,
          per_page: itemsPerPage.value,
          search: filters.search || undefined,
          start_date: filters.startDate || undefined,
          end_date: filters.endDate || undefined,
          status: filters.status || undefined,
        };

        const response = await orderService.getOrders(params);
        orders.value = response.data.data;
        totalItems.value = response.data.total;
      } catch (error) {
        console.error('Error loading orders:', error);
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

    const viewOrder = id => {
      router.push(`/orders/${id}`);
    };

    const openCreateDialog = () => {
      orderDialog.order = null;
      orderDialog.mode = 'create';
      orderDialog.show = true;
    };

    const editOrder = async order => {
      try {
        // Show loading state
        orderDialog.loading = true;

        // Load the complete order with products before editing
        const response = await orderService.getOrder(order.id);
        const completeOrder = response.data;

        orderDialog.order = completeOrder;
        orderDialog.mode = 'edit';
        orderDialog.show = true;
      } catch (error) {
        console.error('Error loading order for editing:', error);
        showApiError(notificationStore, error, t, 'messages.loadOrdersFailed');
      } finally {
        orderDialog.loading = false;
      }
    };

    const deleteOrder = order => {
      deleteDialog.order = order;
      deleteDialog.show = true;
    };

    const confirmDelete = async () => {
      deleteDialog.loading = true;
      try {
        await orderService.deleteOrder(deleteDialog.order.id);
        notificationStore.showSuccess(t('messages.orderDeleted'));
        deleteDialog.show = false;
        loadOrders();
      } catch (error) {
        console.error('Error deleting order:', error);
        showApiError(notificationStore, error, t, 'messages.deleteOrderFailed');
      } finally {
        deleteDialog.loading = false;
      }
    };

    const onOrderSaved = () => {
      orderDialog.show = false;
      loadOrders();
    };

    onMounted(() => {
      // Orders will be loaded by v-data-table-server @update:options
    });

    return {
      loading,
      orders,
      totalItems,
      page,
      itemsPerPage,
      filters,
      orderDialog,
      deleteDialog,
      headers,
      statusOptions,
      debouncedSearch,
      loadOrders,
      formatDate,
      getStatusColor,
      viewOrder,
      openCreateDialog,
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
</style>
