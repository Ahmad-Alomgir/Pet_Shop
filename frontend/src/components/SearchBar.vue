<template>
  <div class="search-bar">
    <input
      type="text"
      v-model="query"
      @input="handleSearch"
      placeholder="Search by brand, type, or pet..."
    />

    <div class="results" v-if="results.length">
      <div
        class="result-item"
        v-for="item in results"
        :key="item.id"
        @click="goToProduct(item.id)"
      >
        {{ item.name }}
      </div>
    </div>
  </div>
</template>
   
   <script>
   import { searchProducts } from '../services/api'
   
   export default {
     data() {
       return {
         query: '',
         results: [],
         timeout: null
       }
     },
   
     methods: {
       handleSearch() {
         clearTimeout(this.timeout)
   
         if (!this.query) {
           this.results = []
           return
         }
   
         this.timeout = setTimeout(async () => {
           const res = await searchProducts(this.query)
           this.results = res.data.data
         }, 300)
       },
   
       goToProduct(id) {
         this.results = []
         this.query = ''
         this.$router.push(`/product/${id}`)
       }
     }
   }
   </script>
   
<style scoped>
.search-bar {
  position: relative;
  margin: 12px 0 18px;
}

.results {
  position: absolute;
  width: 100%;
  margin-top: 6px;
  background: #fff;
  border: 1px solid #dbe3ef;
  border-radius: 10px;
  z-index: 10;
  overflow: hidden;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
}

.result-item {
  padding: 11px 12px;
  cursor: pointer;
}

.result-item:hover {
  background: #f8fafc;
}
</style>