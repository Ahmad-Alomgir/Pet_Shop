<template>
  <div class="toast-host">
    <div v-for="toast in toasts" :key="toast.id" class="toast" :class="toast.type">
      {{ toast.message }}
    </div>
  </div>
</template>

<script>
import toastStore from '../store/toast'

export default {
  computed: {
    toasts() {
      return toastStore.state.toasts
    }
  }
}
</script>

<style scoped>
.toast-host {
  position: fixed;
  right: 16px;
  top: 16px;
  z-index: 9999;
  display: grid;
  gap: 10px;
}

.toast {
  min-width: 280px;
  max-width: 380px;
  border-radius: 12px;
  padding: 12px 14px;
  backdrop-filter: blur(10px);
  border: 1px solid transparent;
  color: #e2e8f0;
  background: rgba(15, 23, 42, 0.88);
  box-shadow: 0 16px 32px rgba(2, 6, 23, 0.35);
}

.toast.success {
  border-color: #34d399;
}

.toast.error {
  border-color: #f87171;
}

.toast.info {
  border-color: #60a5fa;
}

@media (max-width: 720px) {
  .toast-host {
    left: 12px;
    right: 12px;
    top: 12px;
  }

  .toast {
    min-width: 0;
    max-width: 100%;
  }
}
</style>
