<template>
  <div class="container page" v-if="product">
    <div class="details card">
      <img :src="imageUrl(product.image)" />

      <div class="info">
        <h2>{{ product.name }}</h2>
        <p class="rating">★★★★☆ <span>4.4 out of 5 | 218 ratings</span></p>
        <p class="meta">Brand: PetStore Choice | Category: {{ product.category_name || 'Pet Food' }}</p>
        <p class="description">{{ product.description }}</p>
        <h3 class="price">৳{{ formatBDT(product.price) }}</h3>
        <p class="stock">In stock. Ready to ship.</p>
        <div class="actions">
          <button class="add-btn" @click="addToCart">Add to Cart</button>
          <button class="buy-btn" @click="buyNow">Buy Now</button>
        </div>
      </div>
    </div>

    <div class="card section">
      <h3>About this item</h3>
      <ul class="bullets">
        <li>Premium ingredients formulated for healthy digestion and coat quality.</li>
        <li>Balanced nutrition for daily feeding and long-term vitality.</li>
        <li>Trusted by thousands of pet parents in our community.</li>
      </ul>
    </div>

    <div class="card section">
      <h3>Customer Reviews</h3>
      <div class="review" v-for="(review, idx) in reviews" :key="idx">
        <p class="review-head"><strong>{{ review.name }}</strong> <span>{{ review.rating }}</span></p>
        <p>{{ review.comment }}</p>
      </div>
    </div>
  </div>

  <Loader :show="loading" />
</template>
   
   <script>
   import { getProduct } from '../services/api'
   import cartStore from '../store/cart'
   import Loader from '../components/Loader.vue'
   import toastStore from '../store/toast'
   import { formatBDT } from '../utils/format'
   
   export default {
     components: { Loader },
   
     data() {
       return {
         product: null,
        loading: false,
        reviews: [
          { name: 'Sadia H.', rating: '★★★★★', comment: 'My cat loved it from day one. Great quality and fast delivery.' },
          { name: 'Rahim K.', rating: '★★★★☆', comment: 'Good product and packaging. Would buy again.' },
          { name: 'Nadia S.', rating: '★★★★★', comment: 'Worth the price. Noticeable improvement in my dog energy.' }
        ]
       }
     },
   
     methods: {
       async loadProduct() {
         this.loading = true
         const id = this.$route.params.id
         const res = await getProduct(id)
         this.product = res.data.data
         this.loading = false
       },
   
       imageUrl(image) {
         return `http://localhost/pet-food-store/backend/uploads/products/${image}`
       },
   
       async addToCart() {
         await cartStore.add(this.product.id, 1)
        toastStore.success('Added to cart successfully')
      },
      async buyNow() {
        await cartStore.add(this.product.id, 1)
        toastStore.success('Taking you to checkout...')
        this.$router.push('/checkout')
      },
      formatBDT
    },
  
    mounted() {
      this.loadProduct()
    }
  }
  </script>
   
<style scoped>
.details {
  display: grid;
  grid-template-columns: minmax(220px, 360px) 1fr;
  gap: 22px;
}

.details img {
  width: 100%;
  border-radius: 12px;
  object-fit: cover;
}

.info {
  max-width: 640px;
}

.description {
  margin: 10px 0 16px;
  color: #475569;
  line-height: 1.6;
}

.price {
  margin-bottom: 6px;
  color: #b45309;
}

.rating {
  color: #f59e0b;
  margin: 6px 0;
}

.rating span {
  color: #475569;
  margin-left: 8px;
  font-size: 0.9rem;
}

.meta {
  color: #334155;
  font-size: 0.92rem;
}

.stock {
  color: #047857;
  margin-bottom: 12px;
}

.actions {
  display: flex;
  gap: 10px;
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

.section {
  margin-top: 16px;
}

.bullets {
  padding-left: 18px;
  display: grid;
  gap: 8px;
  color: #334155;
}

.review {
  border-top: 1px solid #e2e8f0;
  padding: 12px 0;
}

.review-head {
  display: flex;
  gap: 10px;
  align-items: center;
  color: #f59e0b;
}

@media (max-width: 820px) {
  .details {
    grid-template-columns: 1fr;
  }
}
</style>