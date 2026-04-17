<?php

class CustomerController extends Controller
{
    public function __construct()
    {
        UserMiddleware::handle();
    }

    public function profile()
    {
        $this->json([
            'status' => 'success',
            'data' => $_SESSION['user']
        ]);
    }

    public function updateProfile()
    {
        $data = $this->input();
        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if ($name === '') {
            return $this->json([
                'status' => 'error',
                'message' => 'Name is required'
            ], 400);
        }

        $userModel = $this->model('User');
        $userModel->updateProfile($_SESSION['user']['id'], $name, $phone);

        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['phone'] = $phone;

        return $this->json([
            'status' => 'success',
            'message' => 'Profile updated',
            'data' => $_SESSION['user']
        ]);
    }

    public function orders()
    {
        $orderModel = $this->model('Order');
        $orderItemModel = $this->model('OrderItem');
        $orders = $orderModel->getByUser($_SESSION['user']['id']);

        foreach ($orders as &$order) {
            $order['items'] = $orderItemModel->getByOrder($order['id']);
        }

        $this->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }
}
