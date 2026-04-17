<template>
  <div class="container page">
    <h2 class="section-title">My Account</h2>
    <p class="subtext">Manage profile details and track your orders.</p>

    <div class="account-layout">
      <section class="card">
        <h3>Profile Information</h3>
        <p v-if="message" class="success-box">{{ message }}</p>
        <p v-if="error" class="error-box">{{ error }}</p>
        <form @submit.prevent="saveProfile">
          <div class="form-group">
            <label>Name</label>
            <input v-model="profile.name" required />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="profile.email" disabled />
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input v-model="profile.phone" />
          </div>
          <button type="submit" :disabled="saving">{{ saving ? 'Saving...' : 'Save Changes' }}</button>
        </form>
      </section>

      <section class="card">
        <h3>Order History</h3>
        <div v-if="orders.length" class="orders">
          <div class="order-card" v-for="order in orders" :key="order.id">
            <div class="order-head">
              <strong>Order #{{ order.id }}</strong>
              <span class="status">{{ order.status }}</span>
            </div>
            <p class="meta">Placed: {{ formatDate(order.created_at) }}</p>
            <p class="meta">Total: ৳{{ formatBDT(order.total_amount) }}</p>
            <p class="meta">Shipping: {{ order.customer_address }}</p>

            <div class="items">
              <p class="items-title">Items</p>
              <div class="item" v-for="item in order.items" :key="item.id">
                <span>{{ item.name }}</span>
                <span>{{ item.quantity }} x ৳{{ formatBDT(item.price) }}</span>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="subtext">No orders yet. Start shopping to see orders here.</p>
      </section>
    </div>
  </div>
</template>

<script>
import authStore from '../store/auth'
import { getCustomerOrders, getCustomerProfile, updateCustomerProfile } from '../services/api'
import { formatBDT } from '../utils/format'

export default {
  data() {
    return {
      profile: {
        name: '',
        email: '',
        phone: ''
      },
      orders: [],
      saving: false,
      message: '',
      error: ''
    }
  },
  methods: {
    async loadData() {
      const [profileRes, ordersRes] = await Promise.all([
        getCustomerProfile(),
        getCustomerOrders()
      ])
      this.profile = {
        name: profileRes.data.data.name || '',
        email: profileRes.data.data.email || '',
        phone: profileRes.data.data.phone || ''
      }
      this.orders = ordersRes.data.data || []
    },
    async saveProfile() {
      this.message = ''
      this.error = ''
      try {
        this.saving = true
        const res = await updateCustomerProfile({
          name: this.profile.name,
          phone: this.profile.phone
        })
        authStore.state.user = res.data.data
        this.message = 'Profile updated successfully.'
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update profile'
      } finally {
        this.saving = false
      }
    },
    formatDate(value) {
      if (!value) return '-'
      return new Date(value).toLocaleString()
    },
    formatBDT
  },
  async mounted() {
    await this.loadData()
  }
}
</script>

<style scoped>
.subtext {
  color: #64748b;
  margin-bottom: 12px;
}

.account-layout {
  display: grid;
  grid-template-columns: minmax(280px, 360px) 1fr;
  gap: 14px;
}

.orders {
  display: grid;
  gap: 12px;
}

.order-card {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 12px;
  background: #f8fafc;
}

.order-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.status {
  text-transform: capitalize;
  background: #dbeafe;
  color: #1e3a8a;
  padding: 4px 8px;
  border-radius: 999px;
  font-size: 0.82rem;
}

.meta {
  color: #475569;
  margin-bottom: 4px;
}

.items {
  margin-top: 8px;
  border-top: 1px dashed #cbd5e1;
  padding-top: 8px;
}

.items-title {
  font-weight: 600;
  margin-bottom: 4px;
}

.item {
  display: flex;
  justify-content: space-between;
  color: #334155;
  font-size: 0.92rem;
}

.success-box,
.error-box {
  margin-bottom: 10px;
  padding: 10px;
  border-radius: 10px;
}

.success-box {
  color: #166534;
  background: #dcfce7;
  border: 1px solid #bbf7d0;
}

.error-box {
  color: #b91c1c;
  background: #fee2e2;
  border: 1px solid #fecaca;
}

@media (max-width: 960px) {
  .account-layout {
    grid-template-columns: 1fr;
  }
}
</style>
