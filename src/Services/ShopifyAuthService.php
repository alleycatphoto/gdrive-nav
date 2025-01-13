<?php
namespace App\Services;

class ShopifyAuthService {
    private $apiKey;
    private $apiPassword;
    private $apiUrl;

    public function __construct() {
        $this->apiKey = $_ENV['SHOPIFY_API_KEY'];
        $this->apiPassword = $_ENV['SHOPIFY_API_PASSWORD'];
        $this->apiUrl = $_ENV['SHOPIFY_API_URL'];
    }

    public function getCurrentUser() {
        if (!isset($_SESSION['shopify_user'])) {
            return null;
        }
        return $_SESSION['shopify_user'];
    }

    public function login() {
        try {
            // Make API call to Shopify to verify credentials and get user info
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/api/2024-01/users/current.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ':' . $this->apiPassword);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $userData = json_decode($result, true);
                $_SESSION['shopify_user'] = $userData;
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log("Shopify login error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        unset($_SESSION['shopify_user']);
        session_regenerate_id(true);
        return true;
    }

    public function isAuthenticated() {
        return isset($_SESSION['shopify_user']);
    }
}
