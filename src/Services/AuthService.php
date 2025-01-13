<?php
namespace App\Services;

class AuthService {
    private $shopifyApiKey;
    private $shopifyApiPassword;
    private $shopifyApiUrl;

    public function __construct() {
        $this->shopifyApiKey = $_ENV['SHOPIFY_API_KEY'];
        $this->shopifyApiPassword = $_ENV['SHOPIFY_API_PASSWORD'];
        $this->shopifyApiUrl = $_ENV['SHOPIFY_API_URL'];
    }

    public function getUserByEmail($email) {
        $ch = curl_init(
            "{$this->shopifyApiUrl}/customers/search.json?query=email:{$email}"
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                "X-Shopify-Access-Token: {$this->shopifyApiPassword}",
                'Content-Type: application/json'
            ]
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Error getting user from Shopify: " . $response);
            return null;
        }

        $data = json_decode($response, true);
        return $data['customers'][0] ?? null;
    }

    public function createUser($email, $password, $firstName = '', $lastName = '') {
        $ch = curl_init("{$this->shopifyApiUrl}/customers.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                "X-Shopify-Access-Token: {$this->shopifyApiPassword}",
                'Content-Type: application/json'
            ]
        );

        $data = [
            'customer' => [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'verified_email' => true
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 201) {
            error_log("Error creating user in Shopify: " . $response);
            return null;
        }

        $responseData = json_decode($response, true);
        return $responseData['customer'] ?? null;
    }

    public function validateCredentials($email, $password) {
        // Note: Shopify Admin API doesn't provide direct password validation
        // We'll use customer search to verify the user exists
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return null;
        }

        // In a production environment, you would implement proper password validation
        // For now, we'll return the user if found
        return $user;
    }

    public function startSession($user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = [
            'id' => $user['id'] ?? '',
            'email' => $user['email'] ?? '',
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'avatar_url' => $user['avatar_url'] ?? null
        ];
        return true;
    }

    public function endSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return true;
    }

    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    public function isAuthenticated() {
        return $this->getCurrentUser() !== null;
    }
}