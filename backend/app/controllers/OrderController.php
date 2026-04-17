<?php

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store Order
    |--------------------------------------------------------------------------
    */
    public function store()
    {
        $data = $this->input();
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Cart is empty'
            ], 400);
        }

        // Validate customer info
        if (
            empty($data['customer_name']) ||
            empty($data['customer_phone']) ||
            empty($data['customer_address'])
        ) {
            return $this->json([
                'status' => 'error',
                'message' => 'Customer information is required'
            ], 400);
        }

        $orderModel = $this->model('Order');
        $orderItemModel = $this->model('OrderItem');
        $paymentMethodModel = $this->model('PaymentMethod');

        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $paymentMethod = $data['payment_method'] ?? 'cash_on_delivery';
        $activeMethods = $paymentMethodModel->getActive();
        $allowedCodes = array_map(function ($method) {
            return $method['code'];
        }, $activeMethods);
        if (!in_array($paymentMethod, $allowedCodes, true)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid payment method'
            ], 400);
        }

        // Create Order
        $orderId = $orderModel->create([
            'user_id' => $_SESSION['user']['id'] ?? null,
            'customer_name' => $data['customer_name'],
            'customer_email' => $_SESSION['user']['email'] ?? null,
            'customer_phone' => $data['customer_phone'],
            'customer_address' => $data['customer_address'],
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentMethod === 'cash_on_delivery' ? 'pending' : 'unpaid'
        ]);

        // Insert Order Items
        foreach ($cart as $item) {
            $orderItemModel->create([
                'order_id' => $orderId,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        // Clear Cart
        unset($_SESSION['cart']);

        $this->json([
            'status' => 'success',
            'message' => 'Order placed successfully',
            'order_id' => $orderId
        ]);
    }
}