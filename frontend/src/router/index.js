import { createRouter, createWebHistory } from 'vue-router'

import Home from '../pages/Home.vue'
import Shop from '../pages/Shop.vue'
import ProductDetails from '../pages/ProductDetails.vue'
import Cart from '../pages/Cart.vue'
import Checkout from '../pages/Checkout.vue'
import Login from '../pages/Login.vue'
import Signup from '../pages/Signup.vue'
import CustomerDashboard from '../pages/CustomerDashboard.vue'
import AdminLogin from '../pages/AdminLogin.vue'
import AdminDashboard from '../pages/AdminDashboard.vue'
import authStore from '../store/auth'

const routes = [
  { path: '/', component: Home },
  { path: '/shop', component: Shop },
  { path: '/product/:id', component: ProductDetails },
  { path: '/cart', component: Cart },
  { path: '/checkout', component: Checkout, meta: { requiresUser: true } },
  { path: '/account', component: CustomerDashboard, meta: { requiresUser: true } },
  { path: '/login', component: Login },
  { path: '/signup', component: Signup },
  { path: '/admin/login', component: AdminLogin },
  { path: '/admin/dashboard', component: AdminDashboard }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  if (!to.meta.requiresUser) return true

  if (!authStore.state.user) {
    await authStore.loadUser()
  }

  if (!authStore.state.user) {
    return '/login'
  }

  return true
})

export default router