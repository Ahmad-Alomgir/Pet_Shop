<template>
  <div class="card product-card">
    <img :src="imageUrl(product.image)" @click="goToDetails" />
    <p class="category">{{ product.category_name || 'Pet Essentials' }}</p>
    <h3 @click="goToDetails">{{ product.name }}</h3>
    <p class="rating">★★★★☆ <span>(128)</span></p>
    <p class="price">৳{{ formatBDT(product.price) }}</p>
    <p class="delivery">FREE delivery by tomorrow</p>
    <div class="actions">
      <button class="add-btn" @click="addToCart">Add to Cart</button>
      <button class="buy-btn" @click="buyNow">Buy Now</button>
    </div>
  </div>
</template>
   
   <script>
   import cartStore from '../store/cart'
   import toastStore from '../store/toast'
   import { formatBDT } from '../utils/format'
   
   export default {
     props: ['product'],
   
     methods: {
       imageUrl(image) {
         return `http://localhost/pet-food-store/backend/uploads/products/${image}`
       },
   
       goToDetails() {
         this.$router.push(`/product/${this.product.id}`)
       },
   
       async addToCart() {
         await cartStore.add(this.product.id, 1)
        toastStore.success('Added to cart successfully')
      },
      async buyNow() {
        await cartStore.add(this.product.id, 1)
        toastStore.success('Product added. Redirecting to checkout...')
        this.$router.push('/checkout')
      },
      formatBDT
    }
  }
   </script>
   
<style scoped>
.product-card {
  display: grid;
  gap: 8px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.14);
}

.product-card img {
  width: 100%;
  height: 210px;
  object-fit: cover;
  border-radius: 10px;
  cursor: pointer;
}

.price {
  font-size: 1.15rem;
  font-weight: 700;
  color: #0f172a;
}

h3 {
  cursor: pointer;
  font-size: 1rem;
  min-height: 48px;
}

.category {
  font-size: 0.8rem;
  color: #2563eb;
}

.rating {
  font-size: 0.9rem;
  color: #f59e0b;
}

.rating span {
  color: #475569;
}

.delivery {
  font-size: 0.85rem;
  color: #047857;
}

.actions {
  display: grid;
  gap: 8px;
}

.add-btn {
  background: #f59e0b;
  color: #111827;
}

.add-btn:hover {
  background: #d97706;
}

.buy-btn {
  background: #f97316;
}

.buy-btn:hover {
  background: #ea580c;
}
</style>