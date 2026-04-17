<?php

class UserAuthController extends Controller
{
    public function signup()
    {
        $data = $this->input();

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            return $this->json([
                'status' => 'error',
                'message' => 'Name, email and password are required'
            ], 400);
        }

        $userModel = $this->model('User');
        if ($userModel->findByEmail($email)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Email already registered'
            ], 409);
        }

        $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'phone' => $phone
        ]);

        $user = $userModel->findByEmail($email);
        unset($user['password']);
        $_SESSION['user'] = $user;

        return $this->json([
            'status' => 'success',
            'message' => 'Signup successful',
            'user' => $user
        ]);
    }

    public function login()
    {
        $data = $this->input();
        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($email === '' || $password === '') {
            return $this->json([
                'status' => 'error',
                'message' => 'Email and password are required'
            ], 400);
        }

        $userModel = $this->model('User');
        $user = $userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        unset($user['password']);
        $_SESSION['user'] = $user;

        return $this->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user
        ]);
    }

    public function logout()
    {
        unset($_SESSION['user']);

        return $this->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    public function me()
    {
        if (!isset($_SESSION['user'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Not authenticated'
            ], 401);
        }

        return $this->json([
            'status' => 'success',
            'data' => $_SESSION['user']
        ]);
    }
}
