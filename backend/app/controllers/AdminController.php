<?php

class AdminController extends Controller
{
    public function __construct()
    {
        AdminMiddleware::handle();
    }

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    public function products()
    {
        $productModel = $this->model('Product');
        $products = $productModel->getAllWithCategory();

        $this->json(['status' => 'success', 'data' => $products]);
    }

    public function storeProduct()
    {
        $data = $_POST;
        $image = uploadFile($_FILES['image'], PRODUCT_UPLOAD_PATH);

        $productModel = $this->model('Product');
        $productModel->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'image' => $image,
            'category_id' => $data['category_id']
        ]);

        $this->json(['status' => 'success', 'message' => 'Product created']);
    }

    public function updateProduct()
    {
        $data = $_POST;
        $id = $data['id'];

        $image = $data['old_image'];
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $image = uploadFile($_FILES['image'], PRODUCT_UPLOAD_PATH);
        }

        $productModel = $this->model('Product');
        $productModel->update($id, [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'image' => $image,
            'category_id' => $data['category_id']
        ]);

        $this->json(['status' => 'success', 'message' => 'Product updated']);
    }

    public function deleteProduct()
    {
        $data = $this->input();

        $productModel = $this->model('Product');
        $productModel->delete($data['id']);

        $this->json(['status' => 'success', 'message' => 'Product deleted']);
    }

    public function adjustInventory()
    {
        $data = $this->input();
        $productId = (int) ($data['id'] ?? 0);
        $delta = (int) ($data['delta'] ?? 0);
        if (!$productId || !$delta) {
            $this->json(['status' => 'error', 'message' => 'Product and delta are required'], 400);
        }

        $productModel = $this->model('Product');
        $productModel->adjustStock($productId, $delta);
        $this->json(['status' => 'success', 'message' => 'Inventory updated']);
    }

    public function lowStockProducts()
    {
        $productModel = $this->model('Product');
        $this->json(['status' => 'success', 'data' => $productModel->lowStock(50)]);
    }

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */
    public function categories()
    {
        $categoryModel = $this->model('Category');
        $this->json(['status' => 'success', 'data' => $categoryModel->all()]);
    }

    public function storeCategory()
    {
        $data = $_POST;
        $image = uploadFile($_FILES['image'], CATEGORY_UPLOAD_PATH);

        $categoryModel = $this->model('Category');
        $categoryModel->create([
            'name' => $data['name'],
            'image' => $image
        ]);

        $this->json(['status' => 'success', 'message' => 'Category created']);
    }

    public function updateCategory()
    {
        $data = $_POST;
        $id = $data['id'];

        $image = $data['old_image'];
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $image = uploadFile($_FILES['image'], CATEGORY_UPLOAD_PATH);
        }

        $categoryModel = $this->model('Category');
        $categoryModel->update($id, [
            'name' => $data['name'],
            'image' => $image
        ]);

        $this->json(['status' => 'success', 'message' => 'Category updated']);
    }

    public function deleteCategory()
    {
        $data = $this->input();

        $categoryModel = $this->model('Category');
        $categoryModel->delete($data['id']);

        $this->json(['status' => 'success', 'message' => 'Category deleted']);
    }

    /*
    |--------------------------------------------------------------------------
    | Sliders
    |--------------------------------------------------------------------------
    */
    public function sliders()
    {
        $sliderModel = $this->model('Slider');
        $this->json(['status' => 'success', 'data' => $sliderModel->getAll()]);
    }

    public function storeSlider()
    {
        $data = $_POST;
        $image = uploadFile($_FILES['image'], SLIDER_UPLOAD_PATH);

        $sliderModel = $this->model('Slider');
        $sliderModel->create([
            'title' => $data['title'],
            'image' => $image
        ]);

        $this->json(['status' => 'success', 'message' => 'Slider created']);
    }

    public function updateSlider()
    {
        $data = $_POST;
        $id = $data['id'];

        $image = $data['old_image'];
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $image = uploadFile($_FILES['image'], SLIDER_UPLOAD_PATH);
        }

        $sliderModel = $this->model('Slider');
        $sliderModel->update($id, [
            'title' => $data['title'],
            'image' => $image
        ]);

        $this->json(['status' => 'success', 'message' => 'Slider updated']);
    }

    public function deleteSlider()
    {
        $data = $this->input();

        $sliderModel = $this->model('Slider');
        $sliderModel->delete($data['id']);

        $this->json(['status' => 'success', 'message' => 'Slider deleted']);
    }

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */
    public function orders()
    {
        $orderModel = $this->model('Order');
        $orders = $orderModel->getAll();

        $this->json(['status' => 'success', 'data' => $orders]);
    }

    public function updateOrder()
    {
        $data = $this->input();

        $orderModel = $this->model('Order');
        $status = $data['status'] ?? '';
        if (!empty($status)) {
            $orderModel->updateStatus($data['id'], $status);
        }
        if (isset($data['payment_method']) || isset($data['payment_status'])) {
            $orderModel->updatePayment(
                $data['id'],
                $data['payment_method'] ?? 'cash_on_delivery',
                $data['payment_status'] ?? 'pending'
            );
        }
        if (array_key_exists('admin_note', $data)) {
            $orderModel->updateAdminNote($data['id'], trim($data['admin_note']));
        }

        $this->json(['status' => 'success', 'message' => 'Order updated']);
    }

    public function orderDetails()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            $this->json(['status' => 'error', 'message' => 'Order id is required'], 400);
        }

        $orderModel = $this->model('Order');
        $orderItemModel = $this->model('OrderItem');
        $order = $orderModel->getById($id);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $items = $orderItemModel->getByOrder($id);
        $this->json([
            'status' => 'success',
            'data' => [
                'order' => $order,
                'items' => $items
            ]
        ]);
    }

    public function analytics()
    {
        $orderModel = $this->model('Order');
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;
        $summary = $orderModel->getRangeSummary($fromDate, $toDate);
        $topSelling = $this->topSellingProducts();
        $categoryPerformance = $this->categoryPerformance();

        $this->json([
            'status' => 'success',
            'data' => [
                'status_counts' => $orderModel->getStatusCounts(),
                'monthly_sales' => $orderModel->getMonthlySales(),
                'totals' => [
                    'products' => count($productModel->all()),
                    'categories' => count($categoryModel->all()),
                    'orders' => count($orderModel->getAll())
                ],
                'summary' => $summary,
                'top_selling_products' => $topSelling,
                'category_performance' => $categoryPerformance,
                'low_stock_products' => $productModel->lowStock(10)
            ]
        ]);
    }

    public function customers()
    {
        $orderModel = $this->model('Order');
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(50, (int) ($_GET['per_page'] ?? 10)));
        $rows = $orderModel->getTopCustomers(200);

        if ($q !== '') {
            $rows = array_values(array_filter($rows, function ($row) use ($q) {
                $haystack = strtolower(
                    ($row['customer_name'] ?? '') . ' ' .
                    ($row['customer_email'] ?? '') . ' ' .
                    ($row['customer_phone'] ?? '')
                );
                return strpos($haystack, strtolower($q)) !== false;
            }));
        }

        $total = count($rows);
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($rows, $offset, $perPage);

        $this->json([
            'status' => 'success',
            'data' => $paginated,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage
            ]
        ]);
    }

    public function coupons()
    {
        $couponModel = $this->model('Coupon');
        $this->json(['status' => 'success', 'data' => $couponModel->all()]);
    }

    public function storeCoupon()
    {
        $data = $this->input();
        if (empty($data['code'])) {
            $this->json(['status' => 'error', 'message' => 'Coupon code is required'], 400);
        }
        $couponModel = $this->model('Coupon');
        $couponModel->create($data);
        $this->json(['status' => 'success', 'message' => 'Coupon created']);
    }

    public function updateCoupon()
    {
        $data = $this->input();
        $id = (int) ($data['id'] ?? 0);
        if (!$id) {
            $this->json(['status' => 'error', 'message' => 'Coupon id is required'], 400);
        }
        $couponModel = $this->model('Coupon');
        $couponModel->updateCoupon($id, $data);
        $this->json(['status' => 'success', 'message' => 'Coupon updated']);
    }

    public function deleteCoupon()
    {
        $data = $this->input();
        $couponModel = $this->model('Coupon');
        $couponModel->delete((int) ($data['id'] ?? 0));
        $this->json(['status' => 'success', 'message' => 'Coupon deleted']);
    }

    public function paymentMethods()
    {
        $paymentMethodModel = $this->model('PaymentMethod');
        $this->json(['status' => 'success', 'data' => $paymentMethodModel->all()]);
    }

    public function activePaymentMethods()
    {
        $paymentMethodModel = $this->model('PaymentMethod');
        $this->json(['status' => 'success', 'data' => $paymentMethodModel->getActive()]);
    }

    public function updatePaymentMethod()
    {
        $data = $this->input();
        $id = (int) ($data['id'] ?? 0);
        if (!$id) {
            $this->json(['status' => 'error', 'message' => 'Payment method id is required'], 400);
        }
        $paymentMethodModel = $this->model('PaymentMethod');
        $paymentMethodModel->updateMethod($id, $data);
        $this->json(['status' => 'success', 'message' => 'Payment method updated']);
    }

    private function topSellingProducts($limit = 8)
    {
        $db = new Database();
        $db->query("
            SELECT p.id, p.name, SUM(oi.quantity) AS units_sold, SUM(oi.quantity * oi.price) AS revenue
            FROM order_items oi
            LEFT JOIN products p ON p.id = oi.product_id
            GROUP BY p.id, p.name
            ORDER BY units_sold DESC
            LIMIT :limit
        ");
        $db->bind(':limit', (int) $limit);
        return $db->resultSet();
    }

    private function categoryPerformance($limit = 8)
    {
        $db = new Database();
        $db->query("
            SELECT c.id, c.name, COUNT(DISTINCT p.id) AS products_count, COALESCE(SUM(oi.quantity), 0) AS units_sold
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            LEFT JOIN order_items oi ON oi.product_id = p.id
            GROUP BY c.id, c.name
            ORDER BY units_sold DESC, products_count DESC
            LIMIT :limit
        ");
        $db->bind(':limit', (int) $limit);
        return $db->resultSet();
    }
}