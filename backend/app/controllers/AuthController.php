<?php

class AuthController extends Controller
{
    public function login()
    {
        try {
            $data = $this->input();

            $email = isset($data['email']) ? trim($data['email']) : '';
            $password = isset($data['password']) ? trim($data['password']) : '';

            if ($email === '' || $password === '') {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Email and password are required'
                ], 400);
            }

            $adminModel = $this->model('Admin');
            $admin = $adminModel->findByEmail($email);

            if (!$admin) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Admin email not found'
                ], 401);
            }

            if (!isset($admin['password']) || !password_verify($password, $admin['password'])) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Invalid password'
                ], 401);
            }

            unset($admin['password']);

            Auth::attempt($admin);

            return $this->json([
                'status' => 'success',
                'message' => 'Login successful',
                'admin' => $admin
            ]);
        } catch (Throwable $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        Auth::logout();

        return $this->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    public function me()
    {
        if (!Auth::check()) {
            return $this->json([
                'status' => 'error',
                'message' => 'Not authenticated'
            ], 401);
        }

        return $this->json([
            'status' => 'success',
            'data' => Auth::user()
        ]);
    }
}