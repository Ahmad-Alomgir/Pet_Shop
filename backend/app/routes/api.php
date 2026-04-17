<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/
$router->get('', 'HomeController@index');


/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
$router->get('products', 'ProductController@index');
$router->get('product', 'ProductController@show'); // ?id=1


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/
$router->get('categories', 'CategoryController@index');


/*
|--------------------------------------------------------------------------
| Slider
|--------------------------------------------------------------------------
*/
$router->get('sliders', 'HomeController@sliders');


/*
|--------------------------------------------------------------------------
| Search (AJAX)
|--------------------------------------------------------------------------
*/
$router->get('search', 'SearchController@index'); // ?q=dog


/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/
$router->post('cart/add', 'CartController@add');
$router->get('cart', 'CartController@index');
$router->post('cart/update', 'CartController@update');
$router->delete('cart/remove', 'CartController@remove');


/*
|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/
$router->post('order', 'OrderController@store');


/*
|--------------------------------------------------------------------------
| Admin Auth
|--------------------------------------------------------------------------
*/
$router->post('admin/login', 'AuthController@login');
$router->post('admin/logout', 'AuthController@logout');
$router->get('admin/me', 'AuthController@me');


/*
|--------------------------------------------------------------------------
| Admin Panel (Minimal)
|--------------------------------------------------------------------------
*/
$router->get('admin/products', 'AdminController@products');
$router->post('admin/product/store', 'AdminController@storeProduct');
$router->post('admin/product/update', 'AdminController@updateProduct');
$router->delete('admin/product/delete', 'AdminController@deleteProduct');

$router->get('admin/categories', 'AdminController@categories');
$router->post('admin/category/store', 'AdminController@storeCategory');
$router->post('admin/category/update', 'AdminController@updateCategory');
$router->delete('admin/category/delete', 'AdminController@deleteCategory');

$router->get('admin/sliders', 'AdminController@sliders');
$router->post('admin/slider/store', 'AdminController@storeSlider');
$router->post('admin/slider/update', 'AdminController@updateSlider');
$router->delete('admin/slider/delete', 'AdminController@deleteSlider');

$router->get('admin/orders', 'AdminController@orders');
$router->post('admin/order/update', 'AdminController@updateOrder');
$router->get('admin/order/details', 'AdminController@orderDetails');
$router->post('admin/inventory/adjust', 'AdminController@adjustInventory');
$router->get('admin/inventory/low-stock', 'AdminController@lowStockProducts');
$router->get('admin/analytics', 'AdminController@analytics');
$router->get('admin/customers', 'AdminController@customers');
$router->get('admin/coupons', 'AdminController@coupons');
$router->post('admin/coupon/store', 'AdminController@storeCoupon');
$router->post('admin/coupon/update', 'AdminController@updateCoupon');
$router->delete('admin/coupon/delete', 'AdminController@deleteCoupon');
$router->get('admin/payment-methods', 'AdminController@paymentMethods');
$router->post('admin/payment-method/update', 'AdminController@updatePaymentMethod');
$router->get('payment-methods', 'AdminController@activePaymentMethods');

/*
|--------------------------------------------------------------------------
| Customer Auth
|--------------------------------------------------------------------------
*/
$router->post('auth/signup', 'UserAuthController@signup');
$router->post('auth/login', 'UserAuthController@login');
$router->post('auth/logout', 'UserAuthController@logout');
$router->get('auth/me', 'UserAuthController@me');

/*
|--------------------------------------------------------------------------
| Customer Dashboard
|--------------------------------------------------------------------------
*/
$router->get('customer/profile', 'CustomerController@profile');
$router->post('customer/profile/update', 'CustomerController@updateProfile');
$router->get('customer/orders', 'CustomerController@orders');