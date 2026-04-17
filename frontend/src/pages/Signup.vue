<template>
  <div class="container page">
    <div class="auth-wrap card">
      <h2>Create Account</h2>
      <p class="muted">Join now for a faster shopping experience.</p>

      <form @submit.prevent="submit">
        <p v-if="errors.length" class="error-box">{{ errors[0] }}</p>
        <div class="form-group">
          <label>Full Name</label>
          <input v-model="form.name" required />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" required />
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input v-model="form.phone" />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="password" minlength="6" required />
        </div>
        <button type="submit" :disabled="loading">{{ loading ? 'Creating...' : 'Create Account' }}</button>
      </form>

      <p class="helper">Already have an account? <a @click.prevent="$router.push('/login')">Sign in</a></p>
    </div>
  </div>
</template>

<script>
import authStore from '../store/auth'
import toastStore from '../store/toast'

export default {
  data() {
    return {
      form: { name: '', email: '', phone: '', password: '' },
      loading: false,
      errors: []
    }
  },
  methods: {
    validate() {
      const errors = []
      if (!this.form.name || this.form.name.trim().length < 2) errors.push('Name must be at least 2 characters.')
      if (!this.form.email) errors.push('Email is required.')
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) errors.push('Please enter a valid email address.')
      if (!this.form.password || this.form.password.length < 6) errors.push('Password must be at least 6 characters.')
      this.errors = errors
      return errors.length === 0
    },
    async submit() {
      if (!this.validate()) return
      try {
        this.loading = true
        await authStore.signup(this.form)
        this.$router.push('/checkout')
      } catch (error) {
        const message = error.response?.data?.message || 'Signup failed'
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
  max-width: 460px;
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
