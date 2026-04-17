import { reactive } from 'vue'

const state = reactive({
  toasts: []
})

let seed = 1

export default {
  state,
  show(message, type = 'info', duration = 2800) {
    const id = seed++
    state.toasts.push({ id, message, type })
    setTimeout(() => {
      this.dismiss(id)
    }, duration)
  },
  success(message, duration) {
    this.show(message, 'success', duration)
  },
  error(message, duration) {
    this.show(message, 'error', duration)
  },
  dismiss(id) {
    state.toasts = state.toasts.filter((item) => item.id !== id)
  }
}
