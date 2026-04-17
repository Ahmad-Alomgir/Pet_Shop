<?php

class HomeController extends Controller
{
    public function index()
    {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        $sliderModel = $this->model('Slider');

        $products = $productModel->getAllWithCategory();
        $categories = $categoryModel->all();
        $sliders = $sliderModel->getAll();

        $this->json([
            'status' => 'success',
            'data' => [
                'sliders' => $sliders,
                'categories' => $categories,
                'products' => $products
            ]
        ]);
    }

    public function sliders()
    {
        $sliderModel = $this->model('Slider');
        $sliders = $sliderModel->getAll();

        $this->json([
            'status' => 'success',
            'data' => $sliders
        ]);
    }
}