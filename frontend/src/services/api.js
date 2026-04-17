import axios from 'axios'

const API = axios.create({
  baseURL: 'http://localhost/pet-food-store/backend/public/index.php',
  withCredentials: true
})

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/
export const getHome = () => API.get('/')
export const getSliders = () => API.get('/sliders')

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
export const getProducts = () => API.get('/products')
export const getProduct = (id) => API.get(`/product?id=${id}`)

/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/
export const getCategories = () => API.get('/categories')

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/
export const searchProducts = (q) => API.get(`/search?q=${q}`)

/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/
export const getCart = () => API.get('/cart')
export const addToCart = (data) => API.post('/cart/add', data)
export const updateCart = (data) => API.post('/cart/update', data)
export const removeFromCart = (data) => API.delete('/cart/remove', { data })

/*
|--------------------------------------------------------------------------
| Order
|--------------------------------------------------------------------------
*/
export const placeOrder = (data) => API.post('/order', data)
export const getPaymentMethods = () => API.get('/payment-methods')

/*
|--------------------------------------------------------------------------
| Admin Auth
|--------------------------------------------------------------------------
*/
export const adminLogin = (data) => API.post('/admin/login', data)
export const adminLogout = () => API.post('/admin/logout')
export const getAdminMe = () => API.get('/admin/me')

/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/
export const getAdminProducts = () => API.get('/admin/products')
export const getAdminCategories = () => API.get('/admin/categories')
export const getAdminSliders = () => API.get('/admin/sliders')
export const getAdminOrders = () => API.get('/admin/orders')
export const updateAdminOrder = (data) => API.post('/admin/order/update', data)
export const getAdminOrderDetails = (id) => API.get(`/admin/order/details?id=${id}`)
export const getAdminAnalytics = () => API.get('/admin/analytics')
export const getAdminCustomers = (params = {}) => API.get('/admin/customers', { params })
export const adjustAdminInventory = (data) => API.post('/admin/inventory/adjust', data)
export const getAdminLowStock = () => API.get('/admin/inventory/low-stock')

export const getAdminCoupons = () => API.get('/admin/coupons')
export const createAdminCoupon = (data) => API.post('/admin/coupon/store', data)
export const updateAdminCoupon = (data) => API.post('/admin/coupon/update', data)
export const deleteAdminCoupon = (id) => API.delete('/admin/coupon/delete', { data: { id } })

export const getAdminPaymentMethods = () => API.get('/admin/payment-methods')
export const updateAdminPaymentMethod = (data) => API.post('/admin/payment-method/update', data)

export const createAdminProduct = (formData) => API.post('/admin/product/store', formData)
export const updateAdminProduct = (formData) => API.post('/admin/product/update', formData)
export const deleteAdminProduct = (id) => API.delete('/admin/product/delete', { data: { id } })

export const createAdminCategory = (formData) => API.post('/admin/category/store', formData)
export const updateAdminCategory = (formData) => API.post('/admin/category/update', formData)
export const deleteAdminCategory = (id) => API.delete('/admin/category/delete', { data: { id } })

export const createAdminSlider = (formData) => API.post('/admin/slider/store', formData)
export const updateAdminSlider = (formData) => API.post('/admin/slider/update', formData)
export const deleteAdminSlider = (id) => API.delete('/admin/slider/delete', { data: { id } })

/*
|--------------------------------------------------------------------------
| Customer Auth
|--------------------------------------------------------------------------
*/
export const userSignup = (data) => API.post('/auth/signup', JSON.stringify(data), {
  headers: { 'Content-Type': 'application/json' }
})
export const userLogin = (data) => API.post('/auth/login', JSON.stringify(data), {
  headers: { 'Content-Type': 'application/json' }
})
export const userLogout = () => API.post('/auth/logout')
export const getCurrentUser = () => API.get('/auth/me')

/*
|--------------------------------------------------------------------------
| Customer Dashboard
|--------------------------------------------------------------------------
*/
export const getCustomerProfile = () => API.get('/customer/profile')
export const updateCustomerProfile = (data) => API.post('/customer/profile/update', data)
export const getCustomerOrders = () => API.get('/customer/orders')