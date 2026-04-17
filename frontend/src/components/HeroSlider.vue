<template>
  <div class="container">
    <div class="slider card" v-if="sliders.length">
      <div class="slide" v-for="(slide, index) in sliders" :key="index" v-show="current === index">
        <img :src="imageUrl(slide.image)" />
        <h2 class="title">{{ slide.title }}</h2>
      </div>
    </div>
  </div>
</template>
   
   <script>
   import { getSliders } from '../services/api'
import { getCache, setCache } from '../utils/cache'
   
   export default {
     data() {
       return {
         sliders: [],
         current: 0
       }
     },
   
     methods: {
       async loadSliders() {
        const cached = getCache('home_sliders')
        if (cached) {
          this.sliders = cached
          this.startAutoSlide()
          return
        }

         const res = await getSliders()
         this.sliders = res.data.data
        setCache('home_sliders', this.sliders, 5 * 60 * 1000)
         this.startAutoSlide()
       },
   
       startAutoSlide() {
         setInterval(() => {
           this.current = (this.current + 1) % this.sliders.length
         }, 3000)
       },
   
       imageUrl(image) {
         return `http://localhost/pet-food-store/backend/uploads/sliders/${image}`
       }
     },
   
     mounted() {
       this.loadSliders()
     }
   }
   </script>
   
<style scoped>
.slider {
  position: relative;
  height: 320px;
  overflow: hidden;
  padding: 0;
  margin-top: 18px;
}

.slide {
  position: absolute;
  width: 100%;
  height: 100%;
}

.slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.title {
  position: absolute;
  left: 18px;
  bottom: 18px;
  color: #fff;
  background: rgba(2, 6, 23, 0.55);
  border-radius: 10px;
  padding: 10px 14px;
}
</style>