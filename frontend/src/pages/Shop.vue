<template>
  <div class="container page">
    <h2 class="section-title">Pet Store Marketplace</h2>
    <p class="subtitle">Discover best-selling food, treats, toys, and health essentials.</p>
    <SearchBar />
    <Loader :show="loading" />

    <div class="toolbar card" v-if="!loading">
      <div>
        <strong>{{ filteredProducts.length }}</strong> results
      </div>
      <select v-model="sortBy">
        <option value="featured">Featured</option>
        <option value="price_low">Price: Low to High</option>
        <option value="price_high">Price: High to Low</option>
        <option value="name">Name: A-Z</option>
      </select>
    </div>

    <div class="grid" v-if="!loading">
      <ProductCard
        v-for="product in filteredProducts"
        :key="product.id"
        :product="product"
      />
    </div>
  </div>
</template>
   
   <script>
   import ProductCard from '../components/ProductCard.vue'
   import SearchBar from '../components/SearchBar.vue'
   import Loader from '../components/Loader.vue'
   import { getProducts } from '../services/api'
import { getCache, setCache } from '../utils/cache'
   
   export default {
     components: {
       ProductCard,
       SearchBar,
       Loader
     },
   
     data() {
       return {
         products: [],
        loading: false,
        sortBy: 'featured'
       }
     },
    computed: {
      filteredProducts() {
        const searchFilter = String(this.$route.query.q || '').toLowerCase()
        const categoryFilter = String(this.$route.query.category || '').toLowerCase()
        const items = [...this.products].filter((item) => {
          const categoryMatch = !categoryFilter || String(item.category_name || '').toLowerCase() === categoryFilter
          const searchMatch = !searchFilter || String(item.name || '').toLowerCase().includes(searchFilter)
          return categoryMatch && searchMatch
        })
        if (this.sortBy === 'price_low') {
          return items.sort((a, b) => Number(a.price) - Number(b.price))
        }
        if (this.sortBy === 'price_high') {
          return items.sort((a, b) => Number(b.price) - Number(a.price))
        }
        if (this.sortBy === 'name') {
          return items.sort((a, b) => String(a.name).localeCompare(String(b.name)))
        }
        return items.sort((a, b) => Number(b.id) - Number(a.id))
      }
    },
   
     methods: {
       async loadProducts() {
        const cached = getCache('shop_products')
        if (cached) {
          this.products = cached
          return
        }

        this.loading = true
        const res = await getProducts()
        this.products = res.data.data
        setCache('shop_products', this.products, 2 * 60 * 1000)
        this.loading = false
       }
     },
   
     mounted() {
       this.loadProducts()
     }
   }
   </script>
   
<style scoped>
.subtitle {
  margin-top: -6px;
  margin-bottom: 12px;
  color: #64748b;
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

@media (max-width: 700px) {
  .toolbar {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style>