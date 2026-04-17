<template>
  <div class="admin-page">
    <div class="container page">
      <div class="topbar card">
        <div>
          <h2>Admin Command Center</h2>
          <p class="subtitle">Run your store operations, merchandising, and order fulfillment from one view.</p>
        </div>
        <button class="btn-danger" @click="logout">Logout</button>
      </div>

      <div class="stats-grid">
        <div class="card stat">
          <p>Products</p>
          <strong>{{ products.length }}</strong>
          <small>{{ categories.length }} categories</small>
        </div>
        <div class="card stat">
          <p>Revenue</p>
          <strong>৳{{ formatBDT(totalRevenue) }}</strong>
          <small>{{ orders.length }} total orders</small>
        </div>
        <div class="card stat highlight">
          <p>Action Needed</p>
          <strong>{{ pendingOrders }}</strong>
          <small>{{ lowStockCount }} low-stock products</small>
        </div>
      </div>

      <section class="card section quick-actions">
        <button class="quick-btn" @click="goToTab('products')">+ Add Product</button>
        <button class="quick-btn" @click="goToTab('categories')">+ Add Category</button>
        <button class="quick-btn" @click="goToTab('orders')">Review Orders</button>
        <button class="quick-btn" @click="goToTab('inventory')">Restock Alerts</button>
      </section>

      <section class="card section" v-if="recentOrders.length">
        <div class="section-head">
          <h3>Recent Orders Queue</h3>
          <span class="badge">{{ recentOrders.length }} newest</span>
        </div>
        <div class="list">
          <div class="list-item order-row" v-for="o in recentOrders" :key="`recent-${o.id}`">
            <div>
              <strong>#{{ o.id }} - {{ o.customer_name }}</strong>
              <p class="row-meta">{{ o.customer_phone || 'No phone provided' }}</p>
            </div>
            <span>৳{{ formatBDT(o.total_amount) }}</span>
            <span class="status-pill">{{ o.status }}</span>
          </div>
        </div>
      </section>

      <div class="tabs">
        <button :class="{ active: activeTab === 'products' }" @click="activeTab = 'products'">Products</button>
        <button :class="{ active: activeTab === 'categories' }" @click="activeTab = 'categories'">Categories</button>
        <button :class="{ active: activeTab === 'sliders' }" @click="activeTab = 'sliders'">Banners</button>
        <button :class="{ active: activeTab === 'orders' }" @click="activeTab = 'orders'">Orders</button>
        <button :class="{ active: activeTab === 'inventory' }" @click="activeTab = 'inventory'">Inventory</button>
        <button :class="{ active: activeTab === 'coupons' }" @click="activeTab = 'coupons'">Coupons</button>
        <button :class="{ active: activeTab === 'customers' }" @click="activeTab = 'customers'">Customers</button>
        <button :class="{ active: activeTab === 'payments' }" @click="activeTab = 'payments'">Payments</button>
        <button :class="{ active: activeTab === 'analytics' }" @click="activeTab = 'analytics'">Analytics</button>
      </div>

      <section class="card section" v-if="activeTab === 'products'">
        <div class="section-head">
          <h3>Product Catalog Management</h3>
          <span class="badge">{{ filteredProducts.length }} shown</span>
        </div>
        <form class="admin-form" @submit.prevent="submitProduct">
          <div class="form-grid">
            <div class="form-group">
              <label>Name</label>
              <input v-model="productForm.name" required />
            </div>
            <div class="form-group">
              <label>Price</label>
              <input v-model="productForm.price" type="number" step="0.01" min="0" required />
            </div>
            <div class="form-group">
              <label>Stock Qty</label>
              <input v-model="productForm.stock_quantity" type="number" min="0" required />
            </div>
            <div class="form-group">
              <label>Low Stock Alert At</label>
              <input v-model="productForm.low_stock_threshold" type="number" min="0" required />
            </div>
            <div class="form-group">
              <label>Category</label>
              <select v-model="productForm.category_id" required>
                <option value="" disabled>Select category</option>
                <option v-for="c in categories" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
              </select>
            </div>
            <div class="form-group">
              <label>Image</label>
              <input type="file" accept="image/*" @change="onProductImage" />
            </div>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="productForm.description" required></textarea>
          </div>
          <div class="actions">
            <button type="submit">{{ productForm.id ? 'Update Product' : 'Add Product' }}</button>
            <button v-if="productForm.id" type="button" class="btn-secondary" @click="resetProductForm">Cancel Edit</button>
          </div>
        </form>

        <div class="toolbar">
          <input v-model="productSearch" placeholder="Search by name or category..." />
          <select v-model="productSort">
            <option value="latest">Latest First</option>
            <option value="price_desc">Highest Price</option>
            <option value="price_asc">Lowest Price</option>
            <option value="name_asc">A-Z</option>
          </select>
        </div>

        <div v-if="filteredProducts.length" class="list">
          <div class="list-item product-row" v-for="p in filteredProducts" :key="p.id">
            <img :src="productImage(p.image)" alt="" />
            <div>
              <strong>{{ p.name }}</strong>
              <p class="row-meta">{{ p.category_name || 'No category' }}</p>
              <p class="row-meta">Stock: {{ p.stock_quantity }} (alert <= {{ p.low_stock_threshold }})</p>
            </div>
            <span>৳{{ formatBDT(p.price) }}</span>
            <div class="row-actions">
              <button class="btn-secondary" @click="adjustStock(p.id, 1)">+1</button>
              <button class="btn-secondary" @click="adjustStock(p.id, -1)">-1</button>
              <button class="btn-secondary" @click="editProduct(p)">Edit</button>
              <button class="btn-danger" @click="removeProduct(p.id)">Delete</button>
            </div>
          </div>
        </div>
        <p v-else class="empty">No products matched your search.</p>
      </section>

      <section class="card section" v-if="activeTab === 'categories'">
        <h3>Category Management</h3>
        <form class="admin-form" @submit.prevent="submitCategory">
          <div class="form-grid">
            <div class="form-group">
              <label>Name</label>
              <input v-model="categoryForm.name" required />
            </div>
            <div class="form-group">
              <label>Image</label>
              <input type="file" accept="image/*" @change="onCategoryImage" />
            </div>
          </div>
          <div class="actions">
            <button type="submit">{{ categoryForm.id ? 'Update Category' : 'Add Category' }}</button>
            <button v-if="categoryForm.id" type="button" class="btn-secondary" @click="resetCategoryForm">Cancel Edit</button>
          </div>
        </form>

        <div v-if="categories.length" class="list">
          <div class="list-item media-row" v-for="c in categories" :key="c.id">
            <img :src="categoryImage(c.image)" alt="" />
            <span>{{ c.name }}</span>
            <div class="row-actions">
              <button class="btn-secondary" @click="editCategory(c)">Edit</button>
              <button class="btn-danger" @click="removeCategory(c.id)">Delete</button>
            </div>
          </div>
        </div>
        <p v-else class="empty">No categories found.</p>
      </section>

      <section class="card section" v-if="activeTab === 'sliders'">
        <h3>Homepage Banner Management</h3>
        <form class="admin-form" @submit.prevent="submitSlider">
          <div class="form-grid">
            <div class="form-group">
              <label>Title</label>
              <input v-model="sliderForm.title" required />
            </div>
            <div class="form-group">
              <label>Image</label>
              <input type="file" accept="image/*" @change="onSliderImage" />
            </div>
          </div>
          <div class="actions">
            <button type="submit">{{ sliderForm.id ? 'Update Banner' : 'Add Banner' }}</button>
            <button v-if="sliderForm.id" type="button" class="btn-secondary" @click="resetSliderForm">Cancel Edit</button>
          </div>
        </form>

        <div v-if="sliders.length" class="list">
          <div class="list-item media-row" v-for="s in sliders" :key="s.id">
            <img :src="sliderImage(s.image)" alt="" />
            <span>{{ s.title }}</span>
            <div class="row-actions">
              <button class="btn-secondary" @click="editSlider(s)">Edit</button>
              <button class="btn-danger" @click="removeSlider(s.id)">Delete</button>
            </div>
          </div>
        </div>
        <p v-else class="empty">No banners found.</p>
      </section>

      <section class="card section" v-if="activeTab === 'orders'">
        <div class="section-head">
          <h3>Order Operations</h3>
          <span class="badge">{{ filteredOrders.length }} shown</span>
        </div>
        <div class="toolbar">
          <input v-model="orderSearch" placeholder="Search customer name or phone..." />
          <select v-model="orderFilter">
            <option value="all">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div v-if="filteredOrders.length" class="list">
          <div class="list-item order-row" v-for="o in filteredOrders" :key="o.id">
            <div>
              <strong>#{{ o.id }} - {{ o.customer_name }}</strong>
              <p class="row-meta">{{ o.customer_phone || 'N/A' }}</p>
            </div>
            <span>৳{{ formatBDT(o.total_amount) }}</span>
            <select v-model="o.status" @change="changeOrderStatus(o)">
              <option value="pending">pending</option>
              <option value="processing">processing</option>
              <option value="shipped">shipped</option>
              <option value="completed">completed</option>
              <option value="cancelled">cancelled</option>
            </select>
            <select v-model="o.payment_status" @change="updateOrderPayment(o)">
              <option value="pending">pending</option>
              <option value="unpaid">unpaid</option>
              <option value="paid">paid</option>
              <option value="failed">failed</option>
            </select>
            <input v-model="o.admin_note" placeholder="Admin note..." @blur="saveOrderNote(o)" />
            <button class="btn-secondary" @click="openOrderDetails(o)">Details</button>
          </div>
        </div>
        <p v-else class="empty">No orders matched your filters.</p>
        <div v-if="selectedOrder" class="card section">
          <h4>Order #{{ selectedOrder.id }} Details</h4>
          <p class="row-meta">{{ selectedOrder.customer_name }} - {{ selectedOrder.customer_address }}</p>
          <div v-if="orderDetails.length" class="list">
            <div class="list-item analytics-row" v-for="item in orderDetails" :key="item.id">
              <span>{{ item.name }}</span>
              <span>Qty: {{ item.quantity }}</span>
              <span>৳{{ formatBDT(item.price) }}</span>
            </div>
          </div>
        </div>
      </section>

      <section class="card section" v-if="activeTab === 'inventory'">
        <div class="section-head">
          <h3>Inventory Alerts</h3>
          <span class="badge">{{ lowStockCount }} low stock</span>
        </div>
        <div v-if="lowStockProducts.length" class="list">
          <div class="list-item order-row" v-for="item in lowStockProducts" :key="`stock-${item.id}`">
            <div>
              <strong>{{ item.name }}</strong>
              <p class="row-meta">{{ item.category_name || 'No category' }}</p>
            </div>
            <span>{{ item.stock_quantity }} / {{ item.low_stock_threshold }}</span>
            <div class="row-actions">
              <button class="btn-secondary" @click="adjustStock(item.id, 1)">+1</button>
              <button class="btn-secondary" @click="adjustStock(item.id, 10)">+10</button>
            </div>
          </div>
        </div>
        <p v-else class="empty">No low-stock products right now.</p>
      </section>

      <section class="card section" v-if="activeTab === 'coupons'">
        <h3>Coupon Management</h3>
        <form class="admin-form" @submit.prevent="submitCoupon">
          <div class="form-grid">
            <div class="form-group">
              <label>Code</label>
              <input v-model="couponForm.code" required />
            </div>
            <div class="form-group">
              <label>Type</label>
              <select v-model="couponForm.type">
                <option value="percent">Percent</option>
                <option value="fixed">Fixed</option>
              </select>
            </div>
            <div class="form-group">
              <label>Value</label>
              <input v-model="couponForm.value" type="number" min="0" step="0.01" required />
            </div>
            <div class="form-group">
              <label>Min Order</label>
              <input v-model="couponForm.min_order_amount" type="number" min="0" step="0.01" />
            </div>
            <div class="form-group">
              <label>Usage Limit</label>
              <input v-model="couponForm.usage_limit" type="number" min="0" />
            </div>
            <div class="form-group">
              <label>Active</label>
              <select v-model="couponForm.is_active">
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>
          </div>
          <div class="actions">
            <button type="submit">{{ couponForm.id ? 'Update Coupon' : 'Create Coupon' }}</button>
            <button v-if="couponForm.id" type="button" class="btn-secondary" @click="couponForm = defaultCouponForm()">Cancel</button>
          </div>
        </form>
        <div v-if="coupons.length" class="list">
          <div class="list-item analytics-row" v-for="coupon in coupons" :key="coupon.id">
            <span>{{ coupon.code }} ({{ coupon.type }})</span>
            <span>Value: {{ coupon.value }}</span>
            <div class="row-actions">
              <button class="btn-secondary" @click="editCoupon(coupon)">Edit</button>
              <button class="btn-danger" @click="removeCoupon(coupon.id)">Delete</button>
            </div>
          </div>
        </div>
        <p v-else class="empty">No coupons configured.</p>
      </section>

      <section class="card section" v-if="activeTab === 'customers'">
        <div class="section-head">
          <h3>Customer Management</h3>
          <span class="badge">{{ customerMeta.total || 0 }} total</span>
        </div>
        <div class="toolbar">
          <input v-model="customerSearch" placeholder="Search by name, email, phone" />
          <button @click="searchCustomers">Search</button>
        </div>
        <div v-if="customers.length" class="list">
          <div class="list-item customer-row" v-for="c in customers" :key="`${c.customer_email}-${c.customer_phone}-${c.customer_name}`">
            <span>{{ c.customer_name }}</span>
            <span>{{ c.customer_email || 'N/A' }}</span>
            <span>{{ c.orders_count }} orders</span>
            <strong>৳{{ formatBDT(c.total_spent) }}</strong>
          </div>
        </div>
        <p v-else class="empty">No customers found for this query.</p>
        <div class="actions">
          <button class="btn-secondary" @click="prevCustomers">Previous</button>
          <button class="btn-secondary" @click="nextCustomers">Next</button>
        </div>
      </section>

      <section class="card section" v-if="activeTab === 'payments'">
        <h3>Payment Method Configuration</h3>
        <div v-if="paymentMethods.length" class="list">
          <div class="list-item" v-for="method in paymentMethods" :key="method.id">
            <input v-model="method.name" />
            <select v-model.number="method.is_active">
              <option :value="1">Active</option>
              <option :value="0">Inactive</option>
            </select>
            <div class="row-actions">
              <button class="btn-secondary" @click="savePaymentMethod(method)">Save</button>
            </div>
          </div>
        </div>
        <p v-else class="empty">No payment methods configured.</p>
      </section>

      <section class="card section" v-if="activeTab === 'analytics'">
        <h3>Performance Summary</h3>
        <div class="stats-grid">
          <div class="card stat">
            <p>Orders in Range</p>
            <strong>{{ analytics.summary?.orders_count || 0 }}</strong>
          </div>
          <div class="card stat">
            <p>Revenue in Range</p>
            <strong>৳{{ formatBDT(analytics.summary?.revenue || 0) }}</strong>
          </div>
          <div class="card stat">
            <p>Average Order Value</p>
            <strong>৳{{ formatBDT(analytics.summary?.avg_order_value || 0) }}</strong>
          </div>
        </div>
        <h3>Order Status Overview</h3>
        <div v-if="analytics.status_counts.length" class="status-bars">
          <div class="bar-row" v-for="item in analytics.status_counts" :key="item.status">
            <span class="label">{{ item.status }}</span>
            <div class="bar-track">
              <div class="bar-fill" :style="{ width: statusWidth(item.total) }"></div>
            </div>
            <strong>{{ item.total }}</strong>
          </div>
        </div>
        <p v-else class="empty">No order status data available.</p>

        <h3 class="subhead">Monthly Sales</h3>
        <div v-if="analytics.monthly_sales.length" class="list">
          <div class="list-item analytics-row" v-for="m in analytics.monthly_sales" :key="m.month">
            <span>{{ m.month }}</span>
            <span>৳{{ formatBDT(m.total_amount) }}</span>
            <span>{{ m.orders_count }} orders</span>
          </div>
        </div>
        <p v-else class="empty">No monthly data available.</p>

        <h3 class="subhead">Top Selling Products</h3>
        <div v-if="analytics.top_selling_products?.length" class="list">
          <div class="list-item analytics-row" v-for="row in analytics.top_selling_products" :key="`top-${row.id}`">
            <span>{{ row.name }}</span>
            <span>{{ row.units_sold }} units</span>
            <span>৳{{ formatBDT(row.revenue) }}</span>
          </div>
        </div>
        <p v-else class="empty">No top product data available.</p>

        <h3 class="subhead">Category Performance</h3>
        <div v-if="analytics.category_performance?.length" class="list">
          <div class="list-item analytics-row" v-for="row in analytics.category_performance" :key="`cat-${row.id}`">
            <span>{{ row.name }}</span>
            <span>{{ row.products_count }} products</span>
            <span>{{ row.units_sold }} units sold</span>
          </div>
        </div>
        <p v-else class="empty">No category data available.</p>

        <h3 class="subhead">Top Customers</h3>
        <div v-if="customers.length" class="list">
          <div class="list-item customer-row" v-for="c in topCustomers" :key="`${c.customer_phone}-${c.customer_name}`">
            <span>{{ c.customer_name }}</span>
            <span>{{ c.customer_phone || 'N/A' }}</span>
            <span>{{ c.orders_count }} orders</span>
            <strong>৳{{ formatBDT(c.total_spent) }}</strong>
          </div>
        </div>
        <p v-else class="empty">No customer data available.</p>
      </section>
    </div>
  </div>
</template>
   
   <script>
   import {
     getAdminProducts,
     getAdminCategories,
  getAdminSliders,
  getAdminOrders,
  getAdminAnalytics,
  getAdminCustomers,
  adminLogout,
  createAdminProduct,
  updateAdminProduct,
  deleteAdminProduct,
  createAdminCategory,
  updateAdminCategory,
  deleteAdminCategory,
  createAdminSlider,
  updateAdminSlider,
  deleteAdminSlider,
  updateAdminOrder,
  adjustAdminInventory,
  getAdminLowStock,
  getAdminCoupons,
  createAdminCoupon,
  updateAdminCoupon,
  deleteAdminCoupon,
  getAdminPaymentMethods,
  updateAdminPaymentMethod,
  getAdminOrderDetails
   } from '../services/api'
   import toastStore from '../store/toast'
   import { formatBDT } from '../utils/format'
   
export default {
  data() {
    return {
      activeTab: 'products',
      products: [],
      categories: [],
      sliders: [],
      orders: [],
      analytics: {
        status_counts: [],
        monthly_sales: [],
        summary: {},
        top_selling_products: [],
        category_performance: [],
        low_stock_products: []
      },
      customers: [],
      customerSearch: '',
      customerPage: 1,
      customerMeta: { total: 0, page: 1, per_page: 10 },
      lowStockProducts: [],
      coupons: [],
      couponForm: this.defaultCouponForm(),
      paymentMethods: [],
      selectedOrder: null,
      orderDetails: [],
      productSearch: '',
      productSort: 'latest',
      orderSearch: '',
      orderFilter: 'all',
      productForm: this.defaultProductForm(),
      categoryForm: this.defaultCategoryForm(),
      sliderForm: this.defaultSliderForm()
    }
  },
  computed: {
    totalRevenue() {
      return this.orders.reduce((sum, order) => sum + Number(order.total_amount || 0), 0)
    },
    pendingOrders() {
      return this.orders.filter((order) => ['pending', 'processing'].includes(order.status)).length
    },
    completedOrders() {
      return this.orders.filter((order) => order.status === 'completed').length
    },
    lowStockCount() {
      return this.lowStockProducts.length
    },
    recentOrders() {
      return [...this.orders]
        .sort((a, b) => Number(b.id) - Number(a.id))
        .slice(0, 5)
    },
    filteredProducts() {
      const query = this.productSearch.trim().toLowerCase()
      const filtered = this.products.filter((product) => {
        if (!query) return true
        return (
          String(product.name || '').toLowerCase().includes(query) ||
          String(product.category_name || '').toLowerCase().includes(query)
        )
      })

      return filtered.sort((a, b) => {
        if (this.productSort === 'price_desc') return Number(b.price) - Number(a.price)
        if (this.productSort === 'price_asc') return Number(a.price) - Number(b.price)
        if (this.productSort === 'name_asc') return String(a.name).localeCompare(String(b.name))
        return Number(b.id) - Number(a.id)
      })
    },
    filteredOrders() {
      const query = this.orderSearch.trim().toLowerCase()
      return [...this.orders]
        .filter((order) => (this.orderFilter === 'all' ? true : order.status === this.orderFilter))
        .filter((order) => {
          if (!query) return true
          return (
            String(order.customer_name || '').toLowerCase().includes(query) ||
            String(order.customer_phone || '').toLowerCase().includes(query)
          )
        })
        .sort((a, b) => Number(b.id) - Number(a.id))
    },
    topCustomers() {
      return [...this.customers]
        .sort((a, b) => Number(b.total_spent || 0) - Number(a.total_spent || 0))
        .slice(0, 8)
    }
  },

  methods: {
    defaultProductForm() {
      return {
        id: null,
        name: '',
        description: '',
        price: '',
        stock_quantity: 0,
        low_stock_threshold: 5,
        category_id: '',
        image: null,
        old_image: ''
      }
    },
    defaultCategoryForm() {
      return {
        id: null,
        name: '',
        image: null,
        old_image: ''
      }
    },
    defaultSliderForm() {
      return {
        id: null,
        title: '',
        image: null,
        old_image: ''
      }
    },
    defaultCouponForm() {
      return {
        id: null,
        code: '',
        type: 'percent',
        value: '',
        min_order_amount: '',
        usage_limit: '',
        starts_at: '',
        ends_at: '',
        is_active: true
      }
    },
    async loadData() {
      const [p, c, s, o, analytics, customers, lowStock, coupons, paymentMethods] = await Promise.all([
        getAdminProducts(),
        getAdminCategories(),
        getAdminSliders(),
        getAdminOrders(),
        getAdminAnalytics(),
        getAdminCustomers({ q: this.customerSearch, page: this.customerPage, per_page: 10 }),
        getAdminLowStock(),
        getAdminCoupons(),
        getAdminPaymentMethods()
      ])

      this.products = p.data.data
      this.categories = c.data.data
      this.sliders = s.data.data
      this.orders = o.data.data
      this.analytics = analytics.data.data
      this.customers = customers.data.data
      this.customerMeta = customers.data.meta || this.customerMeta
      this.lowStockProducts = lowStock.data.data || []
      this.coupons = coupons.data.data || []
      this.paymentMethods = paymentMethods.data.data || []
    },
    productImage(image) {
      return `http://localhost/pet-food-store/backend/uploads/products/${image}`
    },
    categoryImage(image) {
      return `http://localhost/pet-food-store/backend/uploads/categories/${image}`
    },
    sliderImage(image) {
      return `http://localhost/pet-food-store/backend/uploads/sliders/${image}`
    },
    onProductImage(event) {
      this.productForm.image = event.target.files[0] || null
    },
    onCategoryImage(event) {
      this.categoryForm.image = event.target.files[0] || null
    },
    onSliderImage(event) {
      this.sliderForm.image = event.target.files[0] || null
    },
    buildProductFormData() {
      const formData = new FormData()
      formData.append('name', this.productForm.name)
      formData.append('description', this.productForm.description)
      formData.append('price', this.productForm.price)
      formData.append('stock_quantity', this.productForm.stock_quantity || 0)
      formData.append('low_stock_threshold', this.productForm.low_stock_threshold || 5)
      formData.append('category_id', this.productForm.category_id)
      if (this.productForm.id) {
        formData.append('id', this.productForm.id)
        formData.append('old_image', this.productForm.old_image)
      }
      if (this.productForm.image) {
        formData.append('image', this.productForm.image)
      }
      return formData
    },
    buildCategoryFormData() {
      const formData = new FormData()
      formData.append('name', this.categoryForm.name)
      if (this.categoryForm.id) {
        formData.append('id', this.categoryForm.id)
        formData.append('old_image', this.categoryForm.old_image)
      }
      if (this.categoryForm.image) {
        formData.append('image', this.categoryForm.image)
      }
      return formData
    },
    buildSliderFormData() {
      const formData = new FormData()
      formData.append('title', this.sliderForm.title)
      if (this.sliderForm.id) {
        formData.append('id', this.sliderForm.id)
        formData.append('old_image', this.sliderForm.old_image)
      }
      if (this.sliderForm.image) {
        formData.append('image', this.sliderForm.image)
      }
      return formData
    },
    async submitProduct() {
      try {
        const payload = this.buildProductFormData()
        if (this.productForm.id) {
          await updateAdminProduct(payload)
        } else {
          await createAdminProduct(payload)
        }
        this.resetProductForm()
        await this.loadData()
      } catch (error) {
        toastStore.error(error.response?.data?.message || 'Failed to save product')
      }
    },
    editProduct(product) {
      this.productForm = {
        id: product.id,
        name: product.name,
        description: product.description || '',
        price: product.price,
        stock_quantity: product.stock_quantity ?? 0,
        low_stock_threshold: product.low_stock_threshold ?? 5,
        category_id: String(product.category_id || ''),
        image: null,
        old_image: product.image || ''
      }
      this.activeTab = 'products'
    },
    async removeProduct(id) {
      if (!confirm('Delete this product?')) return
      await deleteAdminProduct(id)
      await this.loadData()
    },
    resetProductForm() {
      this.productForm = this.defaultProductForm()
    },
    async submitCategory() {
      try {
        const payload = this.buildCategoryFormData()
        if (this.categoryForm.id) {
          await updateAdminCategory(payload)
        } else {
          await createAdminCategory(payload)
        }
        this.resetCategoryForm()
        await this.loadData()
      } catch (error) {
        toastStore.error(error.response?.data?.message || 'Failed to save category')
      }
    },
    editCategory(category) {
      this.categoryForm = {
        id: category.id,
        name: category.name,
        image: null,
        old_image: category.image || ''
      }
      this.activeTab = 'categories'
    },
    async removeCategory(id) {
      if (!confirm('Delete this category?')) return
      await deleteAdminCategory(id)
      await this.loadData()
    },
    resetCategoryForm() {
      this.categoryForm = this.defaultCategoryForm()
    },
    async submitSlider() {
      try {
        const payload = this.buildSliderFormData()
        if (this.sliderForm.id) {
          await updateAdminSlider(payload)
        } else {
          await createAdminSlider(payload)
        }
        this.resetSliderForm()
        await this.loadData()
      } catch (error) {
        toastStore.error(error.response?.data?.message || 'Failed to save slider')
      }
    },
    editSlider(slider) {
      this.sliderForm = {
        id: slider.id,
        title: slider.title,
        image: null,
        old_image: slider.image || ''
      }
      this.activeTab = 'sliders'
    },
    async removeSlider(id) {
      if (!confirm('Delete this slider?')) return
      await deleteAdminSlider(id)
      await this.loadData()
    },
    resetSliderForm() {
      this.sliderForm = this.defaultSliderForm()
    },
    async changeOrderStatus(order) {
      await updateAdminOrder({ id: order.id, status: order.status })
    },
    async adjustStock(productId, delta) {
      await adjustAdminInventory({ id: productId, delta })
      await this.loadData()
    },
    async openOrderDetails(order) {
      const res = await getAdminOrderDetails(order.id)
      this.selectedOrder = res.data.data.order
      this.orderDetails = res.data.data.items || []
    },
    async updateOrderPayment(order) {
      await updateAdminOrder({
        id: order.id,
        payment_method: order.payment_method,
        payment_status: order.payment_status
      })
    },
    async saveOrderNote(order) {
      await updateAdminOrder({
        id: order.id,
        admin_note: order.admin_note || ''
      })
    },
    async submitCoupon() {
      if (!this.couponForm.code) return
      const payload = {
        ...this.couponForm,
        is_active: this.couponForm.is_active ? 1 : 0
      }
      if (payload.id) {
        await updateAdminCoupon(payload)
      } else {
        await createAdminCoupon(payload)
      }
      this.couponForm = this.defaultCouponForm()
      await this.loadData()
    },
    editCoupon(coupon) {
      this.couponForm = {
        id: coupon.id,
        code: coupon.code,
        type: coupon.type,
        value: coupon.value,
        min_order_amount: coupon.min_order_amount,
        usage_limit: coupon.usage_limit,
        starts_at: coupon.starts_at || '',
        ends_at: coupon.ends_at || '',
        is_active: Number(coupon.is_active) === 1
      }
      this.activeTab = 'coupons'
    },
    async removeCoupon(id) {
      if (!confirm('Delete this coupon?')) return
      await deleteAdminCoupon(id)
      await this.loadData()
    },
    async savePaymentMethod(method) {
      await updateAdminPaymentMethod({
        id: method.id,
        name: method.name,
        description: method.description,
        sort_order: method.sort_order,
        is_active: Number(method.is_active) === 1 ? 1 : 0
      })
      await this.loadData()
    },
    async searchCustomers() {
      this.customerPage = 1
      await this.loadData()
    },
    async nextCustomers() {
      const totalPages = Math.max(1, Math.ceil((this.customerMeta.total || 0) / (this.customerMeta.per_page || 10)))
      if (this.customerPage >= totalPages) return
      this.customerPage += 1
      await this.loadData()
    },
    async prevCustomers() {
      if (this.customerPage <= 1) return
      this.customerPage -= 1
      await this.loadData()
    },
    statusWidth(total) {
      const max = Math.max(1, ...this.analytics.status_counts.map((i) => Number(i.total)))
      return `${(Number(total) / max) * 100}%`
    },
    formatBDT,
    goToTab(tabName) {
      this.activeTab = tabName
    },

    async logout() {
      await adminLogout()
      this.$router.push('/admin/login')
    }
  },

  async mounted() {
    try {
      await this.loadData()
    } catch (error) {
      if (error.response?.status === 401) {
        this.$router.push('/admin/login')
      }
    }
  }
}
</script>

<style scoped>
.admin-page {
  background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
  min-height: 100vh;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
  border-left: 5px solid #2563eb;
}

.subtitle {
  margin-top: 6px;
  color: #64748b;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
  margin-bottom: 18px;
}

.stat {
  background: linear-gradient(145deg, #ffffff, #f1f5f9);
}

.stat p {
  color: #475569;
}

.stat strong {
  display: block;
  margin-top: 6px;
  font-size: 1.5rem;
}

.stat small {
  color: #64748b;
}

.highlight {
  border-left: 4px solid #f59e0b;
}

.section {
  margin-bottom: 14px;
}

.section h3 {
  margin-bottom: 12px;
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.badge {
  background: #dbeafe;
  color: #1e40af;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 0.8rem;
}

.subhead {
  margin-top: 18px;
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
}

.quick-btn {
  background: #e0f2fe;
  color: #0c4a6e;
}

.tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 14px;
}

.tabs button {
  background: #dbeafe;
  color: #1e3a8a;
}

.tabs button.active {
  background: #2563eb;
  color: #fff;
}

.admin-form {
  margin-bottom: 16px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

textarea {
  min-height: 96px;
}

.actions {
  display: flex;
  gap: 10px;
}

.toolbar {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 10px;
  margin-bottom: 12px;
}

.list {
  display: grid;
  gap: 10px;
}

.list-item {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr;
  gap: 10px;
  align-items: center;
  padding: 10px 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
}

.row-meta {
  margin-top: 4px;
  color: #64748b;
}

.row-actions {
  display: flex;
  gap: 8px;
  justify-self: end;
}

.product-row {
  grid-template-columns: 70px 1.4fr 1fr 1fr;
}

.order-row {
  grid-template-columns: 1.5fr repeat(5, minmax(100px, auto));
}

.analytics-row {
  grid-template-columns: 1fr 1fr 1fr;
}

.media-row {
  grid-template-columns: 70px 1fr auto;
}

.product-row img,
.media-row img {
  width: 58px;
  height: 58px;
  object-fit: cover;
  border-radius: 10px;
}

.empty {
  color: #64748b;
}

.status-pill {
  justify-self: start;
  text-transform: capitalize;
  padding: 4px 10px;
  border-radius: 999px;
  background: #e2e8f0;
  color: #334155;
  font-weight: 600;
}

.status-bars {
  display: grid;
  gap: 10px;
}

.bar-row {
  display: grid;
  grid-template-columns: 110px 1fr 40px;
  align-items: center;
  gap: 10px;
}

.label {
  text-transform: capitalize;
  color: #334155;
}

.bar-track {
  height: 10px;
  border-radius: 999px;
  background: #e2e8f0;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #60a5fa, #2563eb);
}

.customer-row {
  grid-template-columns: 1.2fr 1fr 1fr 1fr;
}

@media (max-width: 900px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .quick-actions {
    grid-template-columns: 1fr 1fr;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .toolbar {
    grid-template-columns: 1fr;
  }

  .list-item {
    grid-template-columns: 1fr;
  }

  .row-actions {
    justify-self: start;
  }
}
</style>