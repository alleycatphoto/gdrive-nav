<?php
namespace App\Controllers;

use App\Services\ShopifyAuthService;

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new ShopifyAuthService();
    }

    public function login() {
        header('Content-Type: application/json');
        
        if ($this->authService->login()) {
            echo json_encode([
                'success' => true,
                'user' => $this->authService->getCurrentUser()
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Authentication failed'
            ]);
        }
    }

    public function logout() {
        header('Content-Type: application/json');
        
        if ($this->authService->logout()) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Logout failed'
            ]);
        }
    }

    public function getCurrentUser() {
        header('Content-Type: application/json');
        
        $user = $this->authService->getCurrentUser();
        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Not authenticated'
            ]);
        }
    }
}
