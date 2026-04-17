import { reactive } from 'vue'
import { getCurrentUser, userLogin, userLogout, userSignup } from '../services/api'

const state = reactive({
  user: null,
  loading: false
})

export default {
  state,

  async loadUser() {
    try {
      const res = await getCurrentUser()
      state.user = res.data.data
    } catch (error) {
      state.user = null
    }
  },

  async login(credentials) {
    state.loading = true
    try {
      const res = await userLogin(credentials)
      state.user = res.data.user
      return res
    } finally {
      state.loading = false
    }
  },

  async signup(payload) {
    state.loading = true
    try {
      const res = await userSignup(payload)
      state.user = res.data.user
      return res
    } finally {
      state.loading = false
    }
  },

  async logout() {
    await userLogout()
    state.user = null
  },

  get isLoggedIn() {
    return !!state.user
  }
}
