<template>
     <div class="cart">
       <h2>Your Cart</h2>
   
       <div v-if="items.length">
         <div class="cart-item" v-for="item in items" :key="item.id">
           <img :src="imageUrl(item.image)" />
           <div>
             <h4>{{ item.name }}</h4>
            <p>{{ item.quantity }} x ৳{{ formatBDT(item.price) }}</p>
           </div>
           <button @click="remove(item.id)">X</button>
         </div>
   
        <h3>Total: ৳{{ formatBDT(total) }}</h3>
   
         <button @click="$router.push('/checkout')">Checkout</button>
       </div>
   
       <p v-else>Cart is empty</p>
     </div>
   </template>
   
   <script>
   import cartStore from '../store/cart'
   import { formatBDT } from '../utils/format'
   
   export default {
     data() {
       return {
         store: cartStore
       }
     },
   
     computed: {
       items() {
         return this.store.state.items
       },
       total() {
         return this.store.total
       }
     },
   
     methods: {
       imageUrl(image) {
         return `http://localhost/pet-food-store/backend/uploads/products/${image}`
       },
       remove(id) {
         this.store.remove(id)
      },
      formatBDT
    }
  }
   </script>
   
   mounted() {
       this.store.loadCart()
   }
   
   <style scoped>
   .cart {
     background: #fff;
     padding: 15px;
   }
   
   .cart-item {
     display: flex;
     align-items: center;
     margin-bottom: 10px;
   }
   
   .cart-item img {
     width: 50px;
     margin-right: 10px;
   }
   
   .cart-item button {
     margin-left: auto;
     background: red;
   }
   </style>