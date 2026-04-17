<template>
  <div class="page">
    <HeroSlider />

    <div class="container">
      <div class="amazon-strip card">
        <div class="strip-item"><strong>Fast Delivery</strong><span>Get pet essentials in 24-48h</span></div>
        <div class="strip-item"><strong>Best Offers</strong><span>Daily deals on premium brands</span></div>
        <div class="strip-item"><strong>Trusted Quality</strong><span>Vet-recommended nutrition</span></div>
        <div class="strip-item"><strong>Secure Checkout</strong><span>Multiple payment methods and trusted processing</span></div>
      </div>
    </div>

    <div class="container">
      <SearchBar />
    </div>

    <CategoryList />

    <div class="container products-section">
      <h2 class="section-title">Trending Deals</h2>
      <p class="subtitle">Best picks for cats, dogs, and small pets.</p>
      <Loader :show="loading" />

      <div class="grid" v-if="!loading">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
        />
      </div>
    </div>

    <div class="container products-section">
      <h2 class="section-title">On Sale</h2>
      <p class="subtitle">Limited-time discounted picks for smart shopping.</p>
      <div class="grid" v-if="saleProducts.length">
        <ProductCard
          v-for="product in saleProducts"
          :key="`sale-${product.id}`"
          :product="product"
        />
      </div>
    </div>

    <div class="container products-section">
      <h2 class="section-title">Popular Categories & Useful Options</h2>
      <div class="options-grid">
        <div class="card option-card">
          <h4>Auto Refill Plans</h4>
          <p>Save time with recurring monthly pet food deliveries.</p>
        </div>
        <div class="card option-card">
          <h4>Vet Picks</h4>
          <p>Nutrition choices curated by trusted veterinary partners.</p>
        </div>
        <div class="card option-card">
          <h4>Budget Deals</h4>
          <p>Affordable combos and discount bundles for daily feeding.</p>
        </div>
        <div class="card option-card">
          <h4>Premium Care</h4>
          <p>High-protein and special diet options for active pets.</p>
        </div>
      </div>
    </div>
  </div>
</template>
   
   <script>
   import HeroSlider from '../components/HeroSlider.vue'
   import CategoryList from '../components/CategoryList.vue'
   import ProductCard from '../components/ProductCard.vue'
   import SearchBar from '../components/SearchBar.vue'
   import Loader from '../components/Loader.vue'
   import { getProducts } from '../services/api'
import { getCache, setCache } from '../utils/cache'
   
   export default {
     components: {
       HeroSlider,
       CategoryList,
       ProductCard,
       SearchBar,
       Loader
     },
   
     data() {
       return {
         products: [],
         loading: false
       }
     },
   
     methods: {
       async loadProducts() {
        const cached = getCache('home_products')
        if (cached) {
          this.products = cached
          return
        }

        this.loading = true
        const res = await getProducts()
        this.products = res.data.data
        setCache('home_products', this.products, 2 * 60 * 1000)
        this.loading = false
       }
    },
    computed: {
      saleProducts() {
        return this.products
          .filter((p) => Number(p.id) % 2 === 0)
          .slice(0, 8)
          .map((p) => ({
            ...p,
            price: (Number(p.price) * 0.9).toFixed(2)
          }))
      }
     },
   
     mounted() {
       this.loadProducts()
     }
   }
   </script>
   
<style scoped>
.products-section {
  margin-top: 12px;
}

.amazon-strip {
  margin-top: 14px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.strip-item {
  display: grid;
  gap: 4px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 12px;
}

.subtitle {
  margin-top: -8px;
  margin-bottom: 12px;
  color: #64748b;
}

.options-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}

.option-card h4 {
  margin-bottom: 8px;
}

.option-card p {
  color: #64748b;
  font-size: 0.92rem;
}

.strip-item span {
  color: #64748b;
  font-size: 0.92rem;
}

@media (max-width: 900px) {
  .amazon-strip {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 680px) {
  .amazon-strip {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 900px) {
  .options-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .options-grid {
    grid-template-columns: 1fr;
  }
}
</style>