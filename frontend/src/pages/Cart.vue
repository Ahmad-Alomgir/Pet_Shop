<template>
  <div class="container page">
    <h2 class="section-title">Shopping Cart</h2>
    <Loader :show="loading" />

    <div v-if="items.length && !loading" class="layout">
      <div class="card cart-card">
        <div class="cart-item" v-for="item in items" :key="item.id">
          <img :src="imageUrl(item.image)" />

          <div class="info">
            <h3>{{ item.name }}</h3>
            <p>Unit Price: ৳{{ formatBDT(item.price) }}</p>
            <div class="qty-control">
              <button class="qty-btn" @click="changeQty(item, item.quantity - 1)">-</button>
              <span>{{ item.quantity }}</span>
              <button class="qty-btn" @click="changeQty(item, item.quantity + 1)">+</button>
            </div>
            <p class="line-total">Subtotal: ৳{{ formatBDT(item.quantity * item.price) }}</p>
          </div>

          <button class="btn-danger" @click="remove(item.id)">Remove</button>
        </div>
      </div>

      <div class="card summary">
        <h3>Order Summary</h3>
        <p><span>Items:</span> <strong>{{ itemsCount }}</strong></p>
        <p><span>Subtotal:</span> <strong>৳{{ formatBDT(total) }}</strong></p>
        <p><span>Shipping:</span> <strong>FREE</strong></p>
        <div class="coupon-box">
          <label>Coupon Code</label>
          <div class="coupon-row">
            <input v-model.trim="couponCode" placeholder="e.g. PET10" />
            <button type="button" class="btn-secondary" @click="applyCoupon">Apply</button>
          </div>
          <p v-if="couponMessage" class="coupon-note">{{ couponMessage }}</p>
        </div>
        <p><span>Discount:</span> <strong>- ৳{{ formatBDT(discountAmount) }}</strong></p>
        <p class="grand"><span>Order Total:</span> <strong>৳{{ formatBDT(payableTotal) }}</strong></p>
        <button @click="$router.push('/checkout')">Proceed to Checkout</button>
        <button class="btn-secondary" @click="$router.push('/shop')">Continue Shopping</button>
      </div>
    </div>

    <p class="empty" v-else-if="!loading">Your cart is currently empty.</p>
  </div>
</template>
   
   <script>
   import cartStore from '../store/cart'
   import Loader from '../components/Loader.vue'
   import toastStore from '../store/toast'
   import { formatBDT } from '../utils/format'
   
   export default {
     components: { Loader },
   
     data() {
       return {
         store: cartStore,
        loading: false,
        couponCode: '',
        discountAmount: 0,
        couponMessage: ''
       }
     },
   
     computed: {
       items() {
         return this.store.state.items
       },
       total() {
         return this.store.total
      },
      itemsCount() {
        return this.items.reduce((sum, item) => sum + Number(item.quantity || 0), 0)
      },
      payableTotal() {
        return Math.max(0, Number(this.total) - Number(this.discountAmount))
       }
     },
   
     methods: {
       imageUrl(image) {
         return `http://localhost/pet-food-store/backend/uploads/products/${image}`
       },
   
       async remove(id) {
         await this.store.remove(id)
        toastStore.info('Item removed from cart')
      },
      async changeQty(item, quantity) {
        await this.store.update(item.id, Number(quantity))
      },
      applyCoupon() {
        const coupons = {
          PET10: 0.1,
          PET20: 0.2,
          FREESHIP: 0.05
        }
        const code = this.couponCode.toUpperCase()
        if (!coupons[code]) {
          this.discountAmount = 0
          this.couponMessage = 'Invalid coupon code.'
          toastStore.error('Invalid coupon code')
          return
        }
        this.discountAmount = Number(this.total) * coupons[code]
        this.couponMessage = `Coupon applied: ${code}`
        toastStore.success(`Coupon ${code} applied successfully`)
      },
      formatBDT
    }
  }
   </script>

  async mounted() {
       this.loading = true
       await this.store.loadCart()
       this.loading = false
    }
   
<style scoped>
.cart-card {
  display: grid;
  gap: 12px;
}

.layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 16px;
  align-items: start;
}

.cart-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #f8fafc;
}

.cart-item img {
  width: 86px;
  height: 86px;
  object-fit: cover;
  border-radius: 10px;
}

.info {
  flex: 1;
}

.info p {
  color: #64748b;
  margin-top: 4px;
}

.line-total {
  color: #0f172a !important;
  font-weight: 700;
}

.qty-control {
  margin-top: 6px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  border: 1px solid #dbe3ef;
  border-radius: 999px;
  padding: 2px 10px;
  background: #fff;
}

.qty-btn {
  padding: 2px 8px;
  border-radius: 999px;
  background: #e2e8f0;
  color: #0f172a;
}

.summary {
  display: grid;
  gap: 10px;
}

.summary p {
  display: flex;
  justify-content: space-between;
  color: #334155;
}

.coupon-box {
  border: 1px dashed #3b82f6;
  border-radius: 12px;
  padding: 10px;
  background: rgba(30, 64, 175, 0.05);
}

.coupon-row {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 8px;
  margin-top: 6px;
}

.coupon-note {
  margin-top: 6px;
  color: #0f172a;
  font-size: 0.9rem;
}

.grand {
  border-top: 1px solid #e2e8f0;
  padding-top: 10px;
  font-size: 1.05rem;
}

.empty {
  color: #64748b;
}

@media (max-width: 720px) {
  .layout {
    grid-template-columns: 1fr;
  }
}
</style>