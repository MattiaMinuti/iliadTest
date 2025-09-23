export default {
  // Navigation & Header
  app: {
    title: 'Order Management',
    toggleTheme: 'Cambia tema',
    changeLanguage: 'Cambia lingua'
  },
  
  // Navigation Menu
  nav: {
    orders: 'Ordini',
    products: 'Prodotti'
  },
  
  // Orders Page
  orders: {
    title: 'Gestione Ordini',
    subtitle: 'Visualizza, cerca e gestisci tutti gli ordini dei clienti',
    searchPlaceholder: 'Cerca ordini...',
    startDate: 'Data Inizio',
    endDate: 'Data Fine',
    status: 'Stato',
    newOrder: 'Nuovo Ordine',
    actions: 'Azioni',
    orderName: 'Nome Ordine',
    date: 'Data',
    total: 'Totale',
    viewDetails: 'Visualizza Dettagli',
    edit: 'Modifica',
    delete: 'Elimina'
  },
  
  // Order Detail
  orderDetail: {
    backToOrders: 'Torna agli Ordini',
    orderInformation: 'Informazioni Ordine',
    orderDate: 'Data Ordine',
    totalAmount: 'Importo Totale',
    totalItems: 'Totale Articoli',
    items: 'articoli',
    actions: 'Azioni',
    editOrder: 'Modifica Ordine',
    deleteOrder: 'Elimina Ordine',
    orderProducts: 'Prodotti Ordine',
    noProducts: 'Nessun prodotto trovato in questo ordine.',
    productName: 'Nome Prodotto',
    sku: 'SKU',
    unitPrice: 'Prezzo Unitario',
    quantity: 'Quantità',
    totalPrice: 'Prezzo Totale',
    orderNotFound: 'Ordine Non Trovato',
    orderNotFoundDesc: 'L\'ordine richiesto non è stato trovato.'
  },
  
  // Order Dialog
  orderDialog: {
    createTitle: 'Crea Nuovo Ordine',
    editTitle: 'Modifica Ordine',
    orderName: 'Nome Ordine',
    orderDate: 'Data Ordine',
    description: 'Descrizione',
    totalAmount: 'Importo Totale',
    orderProducts: 'Prodotti Ordine',
    addProduct: 'Aggiungi Prodotto',
    noProducts: 'Nessun prodotto aggiunto a questo ordine ancora. Clicca "Aggiungi Prodotto" per iniziare.',
    product: 'Prodotto',
    stock: 'Scorta',
    cancel: 'Annulla',
    createOrder: 'Crea Ordine',
    updateOrder: 'Aggiorna Ordine'
  },
  
  // Products Page
  products: {
    title: 'Gestione Prodotti',
    subtitle: 'Gestisci inventario prodotti e informazioni',
    searchPlaceholder: 'Cerca prodotti...',
    stockStatus: 'Stato Scorta',
    sortBy: 'Ordina per',
    newProduct: 'Nuovo Prodotto',
    productName: 'Nome Prodotto',
    price: 'Prezzo',
    stock: 'Scorta'
  },
  
  // Product Dialog
  productDialog: {
    createTitle: 'Crea Nuovo Prodotto',
    editTitle: 'Modifica Prodotto',
    productName: 'Nome Prodotto',
    description: 'Descrizione',
    price: 'Prezzo',
    stockQuantity: 'Quantità Scorta',
    cancel: 'Annulla',
    createProduct: 'Crea Prodotto',
    updateProduct: 'Aggiorna Prodotto'
  },
  
  // Status
  status: {
    pending: 'In Attesa',
    processing: 'In Elaborazione',
    completed: 'Completato',
    cancelled: 'Annullato'
  },
  
  // Stock Status
  stockStatus: {
    inStock: 'Disponibile',
    outOfStock: 'Esaurito'
  },
  
  // Sort Options
  sortOptions: {
    name: 'Nome',
    price: 'Prezzo',
    stock: 'Scorta',
    sku: 'SKU'
  },
  
  // Common
  common: {
    loading: 'Caricamento...',
    save: 'Salva',
    cancel: 'Annulla',
    delete: 'Elimina',
    edit: 'Modifica',
    view: 'Visualizza',
    search: 'Cerca',
    filter: 'Filtra',
    clear: 'Pulisci',
    close: 'Chiudi',
    confirm: 'Conferma',
    yes: 'Sì',
    no: 'No'
  },
  
  // Confirmations
  confirmations: {
    deleteOrder: 'Sei sicuro di voler eliminare l\'ordine "{name}"?',
    deleteProduct: 'Sei sicuro di voler eliminare il prodotto "{name}"?',
    deleteWarning: 'Questa azione non può essere annullata.',
    confirmDelete: 'Conferma Eliminazione'
  },
  
  // Messages
  messages: {
    orderCreated: 'Ordine creato con successo',
    orderUpdated: 'Ordine aggiornato con successo',
    orderDeleted: 'Ordine eliminato con successo',
    productCreated: 'Prodotto creato con successo',
    productUpdated: 'Prodotto aggiornato con successo',
    productDeleted: 'Prodotto eliminato con successo',
    loadOrdersFailed: 'Caricamento ordini fallito',
    loadProductsFailed: 'Caricamento prodotti fallito',
    saveOrderFailed: 'Salvataggio ordine fallito',
    saveProductFailed: 'Salvataggio prodotto fallito',
    deleteOrderFailed: 'Eliminazione ordine fallita',
    deleteProductFailed: 'Eliminazione prodotto fallita',
    insufficientStock: 'Scorta insufficiente per il prodotto: {product}',
    addAtLeastOneProduct: 'Aggiungi almeno un prodotto all\'ordine',
    cannotDeleteProduct: 'Impossibile eliminare prodotto associato ad ordini'
  },
  
  // Validation
  validation: {
    required: 'Questo campo è obbligatorio',
    minQuantity: 'La quantità deve essere maggiore di 0',
    minPrice: 'Il prezzo deve essere 0 o maggiore',
    minStock: 'La quantità di scorta deve essere 0 o maggiore'
  }
}
