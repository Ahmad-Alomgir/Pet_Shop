<?php

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get All Products
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $productModel = $this->model('Product');
        $products = $productModel->getAllWithCategory();

        $this->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Product
    |--------------------------------------------------------------------------
    */
    public function show()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            return $this->json([
                'status' => 'error',
                'message' => 'Product ID is required'
            ], 400);
        }

        $productModel = $this->model('Product');
        $product = $productModel->findWithCategory($id);

        if (!$product) {
            return $this->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $this->json([
            'status' => 'success',
            'data' => $product
        ]);
    }
}