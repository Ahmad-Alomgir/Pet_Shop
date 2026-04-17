<template>
  <header class="app-header">
    <div class="container top-strip">
      <p>Free shipping for orders above $49</p>
      <a @click.prevent="$router.push('/admin/login')">Admin</a>
    </div>

    <div class="container header-content">
      <h1 class="brand" @click="$router.push('/')">PETSTORE</h1>

      <form class="search-link" @submit.prevent="submitSearch">
        <input
          v-model.trim="searchQuery"
          type="text"
          placeholder="Search pet food, treats, toys, and accessories"
        />
        <button type="submit">Search</button>
      </form>

      <nav class="main-nav">
        <a @click.prevent="$router.push('/')">Home</a>
        <a @click.prevent="$router.push('/shop')">Shop</a>
        <a class="cart-link" @click.prevent="$router.push('/cart')">Cart <span>{{ cartCount }}</span></a>
        <a v-if="!isLoggedIn" @click.prevent="$router.push('/login')">Login</a>
        <a v-if="!isLoggedIn" @click.prevent="$router.push('/signup')">Signup</a>
        <a v-if="isLoggedIn" @click.prevent="$router.push('/account')">My Account</a>
        <a v-if="isLoggedIn" class="user-chip">{{ userName }}</a>
        <a v-if="isLoggedIn" @click.prevent="logout">Logout</a>
      </nav>
    </div>
  </header>
</template>

<script>
import cartStore from '../store/cart'
import authStore from '../store/auth'

export default {
  data() {
    return {
      searchQuery: ''
    }
  },
  computed: {
    cartCount() {
      return cartStore.state.items.reduce((sum, item) => sum + Number(item.quantity || 0), 0)
    },
    isLoggedIn() {
      return authStore.isLoggedIn
    },
    userName() {
      return authStore.state.user?.name || 'Account'
    }
  },
  methods: {
    submitSearch() {
      if (!this.searchQuery) {
        this.$router.push('/shop')
        return
      }
      this.$router.push({ path: '/shop', query: { q: this.searchQuery } })
    },
    async logout() {
      await authStore.logout()
      this.$router.push('/')
    }
  },
  async mounted() {
    cartStore.loadCart()
    await authStore.loadUser()
  }
}
</script>

<style scoped>
.top-strip {
  display: flex;
  justify-content: space-between;
  align-items: center;
  min-height: 34px;
  font-size: 0.85rem;
  color: #dbeafe;
}

.top-strip a {
  color: #fde68a;
  cursor: pointer;
}

.header-content {
  min-height: 74px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  border-top: 1px solid #1f2937;
}

.brand {
  color: #ffb703;
  font-size: 1.5rem;
  letter-spacing: 0.05em;
  cursor: pointer;
  border: 2px solid #ffb703;
  border-radius: 8px;
  padding: 4px 8px;
}

.search-link {
  flex: 1;
  max-width: 520px;
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid #ffb703;
}

.search-link input {
  border: none;
  border-radius: 0;
  padding: 10px 12px;
}

.search-link input:focus {
  outline: none;
}

.search-link button {
  border-radius: 0;
  background: #ffb703;
  color: #111827;
  padding: 10px 14px;
}

.search-link button:hover {
  background: #f59e0b;
}

.main-nav {
  display: flex;
  align-items: center;
  gap: 12px;
}

.main-nav a {
  color: #f8fafc;
  text-decoration: none;
  padding: 8px 12px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
}

.main-nav a:hover {
  background: #334155;
  color: #fff;
}

.cart-link span {
  margin-left: 6px;
  background: #f59e0b;
  color: #0f172a;
  border-radius: 999px;
  padding: 2px 8px;
  font-size: 0.8rem;
}

.user-chip {
  background: #1e293b;
}

@media (max-width: 980px) {
  .search-link {
    max-width: 100%;
  }
}
</style>