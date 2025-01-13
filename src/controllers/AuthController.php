<?php
namespace App\Controllers;

use App\Services\AuthService;

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['auth_error'] = 'Email and password are required';
            header('Location: /login');
            exit;
        }

        $user = $this->authService->validateCredentials($email, $password);
        if ($user) {
            $this->authService->startSession($user);
            header('Location: /');
        } else {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['auth_error'] = 'Invalid credentials';
            header('Location: /login');
        }
        exit;
    }

    public function logout() {
        $this->authService->endSession();
        header('Location: /login');
        exit;
    }

    public function showLoginForm() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require __DIR__ . '/../views/login.php';
    }
}