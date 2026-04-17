<template>
  <div class="container page">
    <h2 class="section-title">Shop by Category</h2>
    <p class="subtitle">Find everything your pet needs in one place.</p>
    <div class="grid">
      <div class="card category-card" v-for="cat in categories" :key="cat.id" @click="goToShop(cat.name)">
        <img :src="imageUrl(cat.image)" />
        <div class="overlay">
          <h3>{{ cat.name }}</h3>
          <p>Explore deals</p>
        </div>
      </div>
    </div>
  </div>
</template>
   
   <script>
   import { getCategories } from '../services/api'
import { getCache, setCache } from '../utils/cache'
   
   export default {
     data() {
       return {
         categories: []
       }
     },
   
     methods: {
       async loadCategories() {
        const cached = getCache('categories')
        if (cached) {
          this.categories = cached
          return
        }

         const res = await getCategories()
         this.categories = res.data.data
        setCache('categories', this.categories, 5 * 60 * 1000)
       },
   
       imageUrl(image) {
         return `http://localhost/pet-food-store/backend/uploads/categories/${image}`
      },
      goToShop(name) {
        this.$router.push({ path: '/shop', query: { category: name } })
       }
     },
   
     mounted() {
       this.loadCategories()
     }
   }
   </script>
   
<style scoped>
.subtitle {
  margin-top: -6px;
  margin-bottom: 12px;
  color: #64748b;
}

.category-card {
  display: block;
  position: relative;
  overflow: hidden;
  padding: 0;
  cursor: pointer;
}

.category-card img {
  height: 190px;
  width: 100%;
  object-fit: cover;
}

.overlay {
  position: absolute;
  inset: auto 0 0 0;
  padding: 12px;
  color: #fff;
  background: linear-gradient(to top, rgba(15, 23, 42, 0.85), transparent);
}

.overlay p {
  color: #cbd5e1;
  font-size: 0.88rem;
}
</style>