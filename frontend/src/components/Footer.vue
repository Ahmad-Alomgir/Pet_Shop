<template>
  <footer class="app-footer">
    <div class="container footer-content">
      <div class="links-grid">
        <div>
          <h4>Get to Know Us</h4>
          <a @click.prevent="$router.push('/')">About Us</a>
          <a @click.prevent="$router.push('/shop')">Careers</a>
          <a @click.prevent="$router.push('/shop')">PetStore Blog</a>
        </div>
        <div>
          <h4>Quick Links</h4>
          <a @click.prevent="$router.push('/shop')">Shop</a>
          <a @click.prevent="$router.push('/cart')">Cart</a>
          <a @click.prevent="$router.push('/account')">My Account</a>
        </div>
        <div>
          <h4>Customer Policies</h4>
          <a href="#">Refund Policy</a>
          <a href="#">Return & Replacement</a>
          <a href="#">Shipping Policy</a>
          <a href="#">Privacy Policy</a>
        </div>
        <div>
          <h4>Need Help?</h4>
          <p class="muted">Support: support@petstore.com</p>
          <p class="muted">Hotline: +880 1700-000000</p>
        </div>
      </div>
      <p class="copyright">© 2026 Pet Food Store. Fast checkout, secure ordering, reliable delivery.</p>
    </div>
  </footer>
</template>

<script>
export default {}
</script>

<style scoped>
.footer-content {
  display: grid;
  gap: 18px;
}

.links-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
}

h4 {
  margin-bottom: 10px;
  color: #f8fafc;
}

a {
  display: block;
  margin-bottom: 7px;
  color: #cbd5e1;
  text-decoration: none;
  cursor: pointer;
}

a:hover {
  color: #fff;
  text-decoration: underline;
}

.muted {
  color: #94a3b8;
  font-size: 0.92rem;
}

.copyright {
  border-top: 1px solid #334155;
  padding-top: 12px;
  color: #cbd5e1;
  text-align: center;
}

@media (max-width: 900px) {
  .links-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .links-grid {
    grid-template-columns: 1fr;
  }
}
</style>