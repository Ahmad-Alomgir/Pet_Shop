import { reactive } from 'vue'
import { getCart, addToCart, updateCart, removeFromCart } from '../services/api'

const state = reactive({
  items: [],
  loading: false
})

export default {
  state,

  async loadCart() {
    state.loading = true
    try {
      const res = await getCart()
      state.items = res.data.data
    } finally {
      state.loading = false
    }
  },

  async add(product_id, quantity = 1) {
    await addToCart({ product_id, quantity })
    await this.loadCart()
  },

  async remove(product_id) {
    await removeFromCart({ product_id })
    await this.loadCart()
  },

  async update(product_id, quantity) {
    await updateCart({ product_id, quantity })
    await this.loadCart()
  },

  get total() {
    return state.items.reduce((sum, item) => {
      return sum + item.price * item.quantity
    }, 0)
  }
}