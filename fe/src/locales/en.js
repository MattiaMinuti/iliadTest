export default {
  // Navigation & Header
  app: {
    title: 'Order Management',
    toggleTheme: 'Toggle theme',
    changeLanguage: 'Change language'
  },
  
  // Navigation Menu
  nav: {
    orders: 'Orders',
    products: 'Products'
  },
  
  // Orders Page
  orders: {
    title: 'Orders Management',
    subtitle: 'View, search, and manage all customer orders',
    searchPlaceholder: 'Search orders...',
    startDate: 'Start Date',
    endDate: 'End Date',
    status: 'Status',
    newOrder: 'New Order',
    actions: 'Actions',
    orderName: 'Order Name',
    date: 'Date',
    total: 'Total',
    viewDetails: 'View Details',
    edit: 'Edit',
    delete: 'Delete'
  },
  
  // Order Detail
  orderDetail: {
    backToOrders: 'Back to Orders',
    orderInformation: 'Order Information',
    orderDate: 'Order Date',
    totalAmount: 'Total Amount',
    totalItems: 'Total Items',
    items: 'items',
    actions: 'Actions',
    editOrder: 'Edit Order',
    deleteOrder: 'Delete Order',
    orderProducts: 'Order Products',
    noProducts: 'No products found in this order.',
    productName: 'Product Name',
    sku: 'SKU',
    unitPrice: 'Unit Price',
    quantity: 'Quantity',
    totalPrice: 'Total Price',
    orderNotFound: 'Order Not Found',
    orderNotFoundDesc: 'The requested order could not be found.'
  },
  
  // Order Dialog
  orderDialog: {
    createTitle: 'Create New Order',
    editTitle: 'Edit Order',
    orderName: 'Order Name',
    orderDate: 'Order Date',
    description: 'Description',
    totalAmount: 'Total Amount',
    orderProducts: 'Order Products',
    addProduct: 'Add Product',
    noProducts: 'No products added to this order yet. Click "Add Product" to get started.',
    product: 'Product',
    stock: 'Stock',
    cancel: 'Cancel',
    createOrder: 'Create Order',
    updateOrder: 'Update Order'
  },
  
  // Products Page
  products: {
    title: 'Products Management',
    subtitle: 'Manage product inventory and information',
    searchPlaceholder: 'Search products...',
    stockStatus: 'Stock Status',
    sortBy: 'Sort By',
    newProduct: 'New Product',
    productName: 'Product Name',
    price: 'Price',
    stock: 'Stock'
  },
  
  // Product Dialog
  productDialog: {
    createTitle: 'Create New Product',
    editTitle: 'Edit Product',
    productName: 'Product Name',
    description: 'Description',
    price: 'Price',
    stockQuantity: 'Stock Quantity',
    cancel: 'Cancel',
    createProduct: 'Create Product',
    updateProduct: 'Update Product'
  },
  
  // Status
  status: {
    pending: 'Pending',
    processing: 'Processing',
    completed: 'Completed',
    cancelled: 'Cancelled'
  },
  
  // Stock Status
  stockStatus: {
    inStock: 'In Stock',
    outOfStock: 'Out of Stock'
  },
  
  // Sort Options
  sortOptions: {
    name: 'Name',
    price: 'Price',
    stock: 'Stock',
    sku: 'SKU'
  },
  
  // Common
  common: {
    loading: 'Loading...',
    save: 'Save',
    cancel: 'Cancel',
    delete: 'Delete',
    edit: 'Edit',
    view: 'View',
    search: 'Search',
    filter: 'Filter',
    clear: 'Clear',
    close: 'Close',
    confirm: 'Confirm',
    yes: 'Yes',
    no: 'No'
  },
  
  // Confirmations
  confirmations: {
    deleteOrder: 'Are you sure you want to delete the order "{name}"?',
    deleteProduct: 'Are you sure you want to delete the product "{name}"?',
    deleteWarning: 'This action cannot be undone.',
    confirmDelete: 'Confirm Delete'
  },
  
  // Messages
  messages: {
    orderCreated: 'Order created successfully',
    orderUpdated: 'Order updated successfully',
    orderDeleted: 'Order deleted successfully',
    productCreated: 'Product created successfully',
    productUpdated: 'Product updated successfully',
    productDeleted: 'Product deleted successfully',
    loadOrdersFailed: 'Failed to load orders',
    loadProductsFailed: 'Failed to load products',
    saveOrderFailed: 'Failed to save order',
    saveProductFailed: 'Failed to save product',
    deleteOrderFailed: 'Failed to delete order',
    deleteProductFailed: 'Failed to delete product',
    insufficientStock: 'Insufficient stock for product: {product}',
    addAtLeastOneProduct: 'Please add at least one product to the order',
    cannotDeleteProduct: 'Cannot delete product that is associated with orders',
    validationFailed: 'Validation failed',
    orderNotFound: 'Order not found',
    productNotFound: 'Product not found',
    serverError: 'Internal server error',
    genericError: 'An error occurred'
  },
  
  // Validation
  validation: {
    required: 'This field is required',
    minQuantity: 'Quantity must be greater than 0',
    minPrice: 'Price must be 0 or greater',
    minStock: 'Stock quantity must be 0 or greater'
  }
}
