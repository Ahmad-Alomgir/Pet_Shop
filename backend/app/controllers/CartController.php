<?php

class CartController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get Cart
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $cart = $_SESSION['cart'] ?? [];

        $this->json([
            'status' => 'success',
            'data' => array_values($cart)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Add to Cart
    |--------------------------------------------------------------------------
    */
    public function add()
    {
        $data = $this->input();

        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId) {
            return $this->json([
                'status' => 'error',
                'message' => 'Product ID is required'
            ], 400);
        }

        $productModel = $this->model('Product');
        $product = $productModel->find($productId);

        if (!$product) {
            return $this->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }

        $this->json([
            'status' => 'success',
            'message' => 'Added to cart',
            'data' => $_SESSION['cart']
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Remove from Cart
    |--------------------------------------------------------------------------
    */
    public function remove()
    {
        $data = $this->input();
        $productId = $data['product_id'] ?? null;

        if (!$productId || !isset($_SESSION['cart'][$productId])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Item not found in cart'
            ], 404);
        }

        unset($_SESSION['cart'][$productId]);

        $this->json([
            'status' => 'success',
            'message' => 'Item removed from cart',
            'data' => $_SESSION['cart']
        ]);
    }

    public function update()
    {
        $data = $this->input();
        $productId = $data['product_id'] ?? null;
        $quantity = (int) ($data['quantity'] ?? 0);

        if (!$productId || !isset($_SESSION['cart'][$productId])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Item not found in cart'
            ], 404);
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }

        $this->json([
            'status' => 'success',
            'message' => 'Cart updated',
            'data' => $_SESSION['cart']
        ]);
    }
}