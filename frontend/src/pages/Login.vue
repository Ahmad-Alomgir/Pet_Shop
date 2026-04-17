<template>
  <div class="container page">
    <div class="auth-wrap card">
      <h2>Sign In</h2>
      <p class="muted">Access your account to track orders and checkout faster.</p>

      <form @submit.prevent="submit">
        <p v-if="errors.length" class="error-box">{{ errors[0] }}</p>
        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" required />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="password" required />
        </div>
        <button type="submit" :disabled="loading">{{ loading ? 'Signing in...' : 'Sign In' }}</button>
      </form>

      <p class="helper">New customer? <a @click.prevent="$router.push('/signup')">Create account</a></p>
    </div>
  </div>
</template>

<script>
import authStore from '../store/auth'
import toastStore from '../store/toast'

export default {
  data() {
    return {
      form: { email: '', password: '' },
      loading: false,
      errors: []
    }
  },
  methods: {
    validate() {
      const errors = []
      if (!this.form.email) errors.push('Email is required.')
      if (!this.form.password) errors.push('Password is required.')
      this.errors = errors
      return errors.length === 0
    },
    async submit() {
      if (!this.validate()) return
      try {
        this.loading = true
        await authStore.login(this.form)
        this.$router.push('/checkout')
      } catch (error) {
        const message = error.response?.data?.message || 'Login failed'
        this.errors = [message]
        toastStore.error(message)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.auth-wrap {
  max-width: 440px;
  margin: 0 auto;
}

.muted {
  color: #64748b;
  margin: 8px 0 16px;
}

.helper {
  margin-top: 14px;
}

.helper a {
  color: #2563eb;
  cursor: pointer;
}

.error-box {
  margin-bottom: 10px;
  color: #b91c1c;
  background: #fee2e2;
  border: 1px solid #fecaca;
  padding: 10px;
  border-radius: 10px;
}
</style>
