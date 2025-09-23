<template>
  <div>
    <v-row class="mb-4">
      <v-col>
        <h1 class="text-h4 font-weight-bold mb-2">Products Management</h1>
        <p class="text-subtitle-1 text-medium-emphasis">
          Manage product inventory and information
        </p>
      </v-col>
    </v-row>

    <!-- Filters and Search -->
    <v-card class="mb-4" elevation="1">
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field
              v-model="filters.search"
              label="Search products..."
              prepend-inner-icon="$magnify"
              variant="outlined"
              density="compact"
              clearable
              @input="debouncedSearch"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="filters.inStock"
              :items="stockOptions"
              label="Stock Status"
              variant="outlined"
              density="compact"
              clearable
              @update:model-value="loadProducts"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="sortBy"
              :items="sortOptions"
              label="Sort By"
              variant="outlined"
              density="compact"
              @update:model-value="loadProducts"
            />
          </v-col>
          <v-col cols="12" md="2">
            <v-btn
              color="primary"
              variant="elevated"
              block
              @click="openCreateDialog"
            >
              <v-icon start>$plus</v-icon>
              New Product
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Products Table -->
    <v-card elevation="1">
      <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :headers="headers"
        :items="products"
        :items-length="totalItems"
        :loading="loading"
        :items-per-page-options="[5, 10, 15, 25, 50]"
        item-value="id"
        @update:options="loadProducts"
      >
        <template v-slot:item.sku="{ item }">
          <v-chip size="small" variant="outlined">
            {{ item.sku }}
          </v-chip>
        </template>

        <template v-slot:item.price="{ item }">
          ${{ parseFloat(item.price).toFixed(2) }}
        </template>

        <template v-slot:item.stock_quantity="{ item }">
          <v-chip
            :color="item.stock_quantity > 0 ? 'success' : 'error'"
            size="small"
            variant="tonal"
          >
            {{ item.stock_quantity }}
          </v-chip>
        </template>

        <template v-slot:item.actions="{ item }">
          <v-btn
            icon
            size="small"
            variant="text"
            @click="editProduct(item)"
          >
            <v-icon>$pencil</v-icon>
            <v-tooltip activator="parent" location="top">Edit</v-tooltip>
          </v-btn>
          <v-btn
            icon
            size="small"
            variant="text"
            color="error"
            @click="deleteProduct(item)"
          >
            <v-icon>$delete</v-icon>
            <v-tooltip activator="parent" location="top">Delete</v-tooltip>
          </v-btn>
        </template>
      </v-data-table-server>
    </v-card>

    <!-- Create/Edit Product Dialog -->
    <v-dialog
      v-model="productDialog.show"
      max-width="600px"
      persistent
    >
      <v-card>
        <v-card-title class="text-h5">
          {{ productDialog.mode === 'create' ? 'Create New Product' : 'Edit Product' }}
        </v-card-title>

        <v-form ref="form" @submit.prevent="saveProduct">
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="productDialog.product.name"
                  label="Product Name *"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="productDialog.product.sku"
                  label="SKU *"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="productDialog.product.price"
                  label="Price *"
                  type="number"
                  step="0.01"
                  min="0"
                  variant="outlined"
                  :rules="[rules.required, rules.minPrice]"
                  prepend-inner-icon="$currencyUsd"
                  required
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="productDialog.product.stock_quantity"
                  label="Stock Quantity *"
                  type="number"
                  min="0"
                  variant="outlined"
                  :rules="[rules.required, rules.minStock]"
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="productDialog.product.description"
                  label="Description"
                  variant="outlined"
                  rows="3"
                />
              </v-col>
            </v-row>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              variant="text"
              @click="productDialog.show = false"
            >
              Cancel
            </v-btn>
            <v-btn
              color="primary"
              type="submit"
              :loading="productDialog.saving"
            >
              {{ productDialog.mode === 'create' ? 'Create Product' : 'Update Product' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog
      v-model="deleteDialog.show"
      max-width="400"
    >
      <v-card>
        <v-card-title>Confirm Delete</v-card-title>
        <v-card-text>
          Are you sure you want to delete the product "{{ deleteDialog.product?.name }}"?
          This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            variant="text"
            @click="deleteDialog.show = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="error"
            variant="text"
            :loading="deleteDialog.loading"
            @click="confirmDelete"
          >
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { productService } from '@/services/products'
import { useNotificationStore } from '@/stores/notification'

export default {
  name: 'ProductsView',
  setup() {
    const notificationStore = useNotificationStore()
    const form = ref(null)

    const loading = ref(false)
    const products = ref([])
    const totalItems = ref(0)
    const page = ref(1)
    const itemsPerPage = ref(15)
    const sortBy = ref('name')

    const filters = reactive({
      search: '',
      inStock: null
    })

    const productDialog = reactive({
      show: false,
      mode: 'create',
      saving: false,
      product: {
        name: '',
        description: '',
        price: 0,
        sku: '',
        stock_quantity: 0
      }
    })

    const deleteDialog = reactive({
      show: false,
      product: null,
      loading: false
    })

    const headers = [
      { title: 'Product Name', key: 'name', sortable: true },
      { title: 'SKU', key: 'sku', sortable: true },
      { title: 'Price', key: 'price', sortable: true },
      { title: 'Stock', key: 'stock_quantity', sortable: true },
      { title: 'Actions', key: 'actions', sortable: false, width: '120px' }
    ]

    const stockOptions = [
      { title: 'In Stock', value: true },
      { title: 'Out of Stock', value: false }
    ]

    const sortOptions = [
      { title: 'Name', value: 'name' },
      { title: 'Price', value: 'price' },
      { title: 'Stock', value: 'stock_quantity' },
      { title: 'SKU', value: 'sku' }
    ]

    const rules = {
      required: (value) => !!value || 'This field is required',
      minPrice: (value) => value >= 0 || 'Price must be 0 or greater',
      minStock: (value) => value >= 0 || 'Stock quantity must be 0 or greater'
    }

    let searchTimeout = null
    const debouncedSearch = () => {
      clearTimeout(searchTimeout)
      searchTimeout = setTimeout(() => {
        loadProducts()
      }, 500)
    }

    const loadProducts = async () => {
      loading.value = true
      try {
        const params = {
          page: page.value,
          per_page: itemsPerPage.value,
          search: filters.search || undefined,
          in_stock: filters.inStock !== null ? filters.inStock : undefined,
          sort_by: sortBy.value
        }

        const response = await productService.getProducts(params)
        products.value = response.data.data
        totalItems.value = response.data.total
      } catch (error) {
        console.error('Error loading products:', error)
        notificationStore.showError('Failed to load products')
      } finally {
        loading.value = false
      }
    }

    const openCreateDialog = () => {
      productDialog.product = {
        name: '',
        description: '',
        price: 0,
        sku: '',
        stock_quantity: 0
      }
      productDialog.mode = 'create'
      productDialog.show = true
    }

    const editProduct = (product) => {
      productDialog.product = { ...product }
      productDialog.mode = 'edit'
      productDialog.show = true
    }

    const saveProduct = async () => {
      const { valid } = await form.value.validate()
      if (!valid) return

      productDialog.saving = true
      try {
        if (productDialog.mode === 'create') {
          await productService.createProduct(productDialog.product)
          notificationStore.showSuccess('Product created successfully')
        } else {
          await productService.updateProduct(productDialog.product.id, productDialog.product)
          notificationStore.showSuccess('Product updated successfully')
        }

        productDialog.show = false
        loadProducts()
      } catch (error) {
        console.error('Error saving product:', error)
        const message = error.response?.data?.message || 'Failed to save product'
        notificationStore.showError(message)
      } finally {
        productDialog.saving = false
      }
    }

    const deleteProduct = (product) => {
      deleteDialog.product = product
      deleteDialog.show = true
    }

    const confirmDelete = async () => {
      deleteDialog.loading = true
      try {
        await productService.deleteProduct(deleteDialog.product.id)
        notificationStore.showSuccess('Product deleted successfully')
        deleteDialog.show = false
        loadProducts()
      } catch (error) {
        console.error('Error deleting product:', error)
        const message = error.response?.data?.message || 'Failed to delete product'
        notificationStore.showError(message)
      } finally {
        deleteDialog.loading = false
      }
    }

    onMounted(() => {
      loadProducts()
    })

    return {
      loading,
      products,
      totalItems,
      page,
      itemsPerPage,
      sortBy,
      filters,
      productDialog,
      deleteDialog,
      headers,
      stockOptions,
      sortOptions,
      rules,
      form,
      debouncedSearch,
      loadProducts,
      openCreateDialog,
      editProduct,
      saveProduct,
      deleteProduct,
      confirmDelete
    }
  }
}
</script>

<style scoped>
.v-card {
  border-radius: 8px;
}
</style>
