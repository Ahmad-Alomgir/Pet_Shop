<?php

/*
|--------------------------------------------------------------------------
| Application Configuration
|--------------------------------------------------------------------------
*/

define('APP_NAME', 'Pet Food Store');
define('APP_URL', 'http://localhost/pet-food-store/backend/public');


/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/

define('DB_HOST', 'localhost');
define('DB_NAME', 'pet_food_store');
define('DB_USER', 'root');
define('DB_PASS', '');

/*
|--------------------------------------------------------------------------
| Upload Paths
|--------------------------------------------------------------------------
*/

define('UPLOAD_PATH', BASE_PATH . '/uploads');

define('PRODUCT_UPLOAD_PATH', UPLOAD_PATH . '/products/');
define('CATEGORY_UPLOAD_PATH', UPLOAD_PATH . '/categories/');
define('SLIDER_UPLOAD_PATH', UPLOAD_PATH . '/sliders/');

/*
|--------------------------------------------------------------------------
| Error Reporting (Development)
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
|--------------------------------------------------------------------------
| Default Timezone
|--------------------------------------------------------------------------
*/

date_default_timezone_set('Asia/Dhaka');