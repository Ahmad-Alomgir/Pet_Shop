<template>
  <div class="admin-auth">
    <div class="auth-card card">
      <h2>Admin Panel</h2>
      <p class="subtitle">Sign in to manage products, categories, and orders.</p>

      <form @submit.prevent="login" class="login-form">
        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" placeholder="admin@store.com" required />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="password" placeholder="Enter your password" required />
        </div>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>
    </div>
  </div>
</template>
   
   <script>
import { adminLogin, getAdminMe } from '../services/api'
import toastStore from '../store/toast'
   
   export default {
     data() {
       return {
         form: {
           email: '',
           password: ''
         },
         loading: false
       }
     },
   
    async mounted() {
      try {
        await getAdminMe()
        this.$router.push('/admin/dashboard')
      } catch (error) {
        // no active admin session
      }
    },
    methods: {
      async login() {
        try {
          this.loading = true
          const res = await adminLogin(this.form)
          toastStore.success(res.data.message || 'Logged in successfully')
          this.$router.push('/admin/dashboard')
        } catch (error) {
          toastStore.error(error.response?.data?.message || error.message || 'Login failed')
        } finally {
          this.loading = false
        }
      }
    }
  }
</script>

<style scoped>
.admin-auth {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 24px;
  background: linear-gradient(145deg, #e2e8f0, #f8fafc);
}

.auth-card {
  width: min(460px, 100%);
  padding: 24px;
}

.subtitle {
  color: #64748b;
  margin: 8px 0 16px;
}
</style>