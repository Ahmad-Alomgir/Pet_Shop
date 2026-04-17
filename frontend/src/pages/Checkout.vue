<template>
  <div class="container page">
    <h2 class="section-title">Checkout</h2>
    <p class="subtitle">Secure checkout powered by PetStore Marketplace</p>

    <form @submit.prevent="submitOrder" class="checkout-form card">
      <div v-if="notice.message" class="notice" :class="notice.type">
        {{ notice.message }}
      </div>

      <div class="form-group">
        <label>Full Name</label>
        <input v-model="form.customer_name" type="text" required />
      </div>

      <div class="row">
        <div class="form-group">
          <label>Country</label>
          <select v-model="form.country" required>
            <option value="Bangladesh">Bangladesh</option>
          </select>
        </div>
        <div class="form-group">
          <label>District</label>
          <select v-model="form.district" required>
            <option value="" disabled>Select district</option>
            <option v-for="d in bdDistricts" :key="d" :value="d">{{ d }}</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label>Country Code</label>
          <select v-model="form.country_code" required>
            <option value="+880">+880</option>
          </select>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input v-model="form.phone_number" type="text" placeholder="1XXXXXXXXX" required />
        </div>
      </div>

      <div class="form-group">
        <label>Street Address</label>
        <textarea v-model="form.street_address" required></textarea>
      </div>

      <div class="form-group">
        <label>Payment Method</label>
        <select v-model="form.payment_method" required>
          <option value="" disabled>Select payment method</option>
          <option v-for="method in paymentMethods" :key="method.id" :value="method.code">
            {{ method.name }}
          </option>
        </select>
      </div>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Placing Order...' : 'Place Order' }}
      </button>
    </form>
  </div>
</template>
   
<script>
import { placeOrder, getPaymentMethods } from '../services/api'
import authStore from '../store/auth'

export default {
  data() {
    return {
      form: {
        customer_name: '',
        country: 'Bangladesh',
        district: '',
        country_code: '+880',
        phone_number: '',
        street_address: '',
        payment_method: ''
      },
      notice: {
        type: '',
        message: ''
      },
      bdDistricts: [
        'Dhaka', 'Chattogram', 'Rajshahi', 'Khulna', 'Barishal', 'Sylhet', 'Rangpur', 'Mymensingh',
        'Comilla', 'Gazipur', 'Narayanganj', 'Bogura', 'Coxs Bazar', 'Jessore', 'Kushtia', 'Noakhali'
      ],
      paymentMethods: [],
      loading: false
    }
  },

  async mounted() {
    await authStore.loadUser()
    try {
      const methods = await getPaymentMethods()
      this.paymentMethods = methods.data.data || []
      if (this.paymentMethods.length) {
        this.form.payment_method = this.paymentMethods[0].code
      }
    } catch (error) {
      this.paymentMethods = []
    }
    if (authStore.state.user) {
      this.form.customer_name = authStore.state.user.name || ''
      this.form.phone_number = String(authStore.state.user.phone || '').replace(/^\+?880/, '')
    }
  },

  methods: {
    validateCheckout() {
      if (!this.form.customer_name.trim()) return 'Full name is required'
      if (!this.form.district) return 'Please select a district'
      if (!/^\d{10}$/.test(this.form.phone_number)) return 'Phone number must be 10 digits after country code'
      if (!this.form.street_address.trim()) return 'Street address is required'
      return ''
    },
    async submitOrder() {
      const validationError = this.validateCheckout()
      if (validationError) {
        this.notice = { type: 'error', message: validationError }
        return
      }

      try {
        this.loading = true
        this.notice = { type: '', message: '' }
        const payload = {
          customer_name: this.form.customer_name.trim(),
          customer_phone: `${this.form.country_code}${this.form.phone_number.trim()}`,
          customer_address: `${this.form.street_address.trim()}, ${this.form.district}, ${this.form.country}`,
          payment_method: this.form.payment_method
        }
        const res = await placeOrder(payload)
        this.notice = { type: 'success', message: res.data.message || 'Order placed successfully.' }
        setTimeout(() => {
          this.$router.push('/')
        }, 900)
      } catch (error) {
        this.notice = { type: 'error', message: error.response?.data?.message || 'Failed to place order' }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
   
<style scoped>
.checkout-form {
  max-width: 760px;
  display: grid;
  gap: 6px;
}

.subtitle {
  margin-top: -6px;
  margin-bottom: 12px;
  color: #64748b;
}

.row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

textarea {
  min-height: 110px;
}

.notice {
  border-radius: 10px;
  padding: 10px 12px;
  font-weight: 600;
  margin-bottom: 6px;
}

.notice.error {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.notice.success {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

@media (max-width: 700px) {
  .row {
    grid-template-columns: 1fr;
  }
}
</style>