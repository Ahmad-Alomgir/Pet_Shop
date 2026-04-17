<?php

class SearchController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AJAX Product Search
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $keyword = $_GET['q'] ?? '';

        if (!$keyword) {
            return $this->json([
                'status' => 'success',
                'data' => []
            ]);
        }

        $productModel = $this->model('Product');
        $results = $productModel->search($keyword);

        $this->json([
            'status' => 'success',
            'data' => $results
        ]);
    }
}