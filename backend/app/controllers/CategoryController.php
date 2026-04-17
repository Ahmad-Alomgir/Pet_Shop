<?php

class CategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get All Categories
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->all();

        $this->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}