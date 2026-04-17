<template>
  <div>
    <ToastHost />
    <Header v-if="!isAdminRoute" />
    <router-view />
    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<script>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import ToastHost from './components/ToastHost.vue'

export default {
  components: {
    Header,
    Footer,
    ToastHost
  },
  computed: {
    isAdminRoute() {
      return this.$route.path.startsWith('/admin')
    }
  }
}
</script>