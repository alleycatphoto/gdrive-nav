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

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';

        if (empty($email) || empty($password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['auth_error'] = 'Email and password are required';
            header('Location: /register');
            exit;
        }

        // Check if user already exists
        $existingUser = $this->authService->getUserByEmail($email);
        if ($existingUser) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['auth_error'] = 'User with this email already exists';
            header('Location: /register');
            exit;
        }

        // Create new user
        $user = $this->authService->createUser($email, $password, $firstName, $lastName);
        if ($user) {
            $this->authService->startSession($user);
            header('Location: /');
        } else {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['auth_error'] = 'Error creating user';
            header('Location: /register');
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

    public function showRegisterForm() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require __DIR__ . '/../views/register.php';
    }
}