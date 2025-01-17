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
        error_log("User Data: " . print_r($response, true));
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
                'verified_email' => true,
                'accepts_marketing' => true,
                'tags' => 'dna-distribution, portal, user',
                'accepts_marketing_updated_at' => date('Y-m-d H:i:s'),
                'marketing_opt_in_level' => 'single_opt_in',
                'marketing_permission_level' => 'unrestricted',
                'sms_marketing_permission_level' => 'unrestricted',
                'tags_updated_at' => date('Y-m-d H:i:s'),
                'company' => 'DNA Distribution',
                'custom_fields[dna_distribution_portal_user_id]' => $email
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

    public function getCustomerMetafieldAndMetaobjects($customerId, $namespace, $key) {
        // GraphQL query to fetch metafield
        $metafieldQuery = <<<GRAPHQL
        query (\$customerId: ID!, \$namespace: String!, \$key: String!) {
            customer(id: \$customerId) {
                metafield(namespace: \$namespace, key: \$key) {
                    value
                    type
                }
            }
        }
        GRAPHQL;

        $metafieldVariables = [
            'customerId' => "gid://shopify/Customer/{$customerId}",
            'namespace' => $namespace,
            'key' => $key
        ];


        // Execute the metafield query
        $metafieldResponse = $this->executeGraphQL($metafieldQuery, $metafieldVariables);
       

        $metafield = $metafieldResponse['data']['customer']['metafield'] ?? null;
         //error_log("metafieldQuery: " . $metafieldResponse);
        if (!$metafield || !isset($metafield['value'])) {
            return ['error' => 'No metafield found for the specified namespace and key.'];
        }

        // Parse metaobject GIDs
        $metaobjectGids = json_decode($metafield['value'], true);

        if (empty($metaobjectGids)) {
            return ['metafield' => $metafield, 'metaobjects' => []];
        }

        // GraphQL query to fetch metaobjects
        $metaobjectQuery = <<<GRAPHQL
        query (\$ids: [ID!]!) {
            nodes(ids: \$ids) {
                ... on Metaobject {
                    id
                    fields {
                        key
                        value
                        type
                    }
                }
            }
        }
        GRAPHQL;

        $metaobjectVariables = ['ids' => $metaobjectGids];
        $metaobjectResponse = $this->executeGraphQL($metaobjectQuery, $metaobjectVariables);

        $metaobjects = $metaobjectResponse['data']['nodes'] ?? [];

        // Return a structured result
        return [
            'metafield' => $metafield,
            'metaobjects' => array_map(function ($metaobject) {
                return [
                    'id' => $metaobject['id'],
                    'fields' => $metaobject['fields']
                ];
            }, $metaobjects)
        ];
    }

    private function executeGraphQL($query, $variables) {
        $ch = curl_init("{$this->shopifyApiUrl}/api/2023-01/graphql.json");
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


        $payload = json_encode(['query' => $query, 'variables' => $variables]);
        //error_log("GraphQL: " . $payload);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        //error_log("GraphQL: " . $response);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("GraphQL API Error: " . $response);
            return null;
        }

        return json_decode($response, true);
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

        
        //error_log(print_r($user));
        return $user;
    }

    public function startSession($user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        error_log("User: " . print_r($user, true));
        $access = $this->getCustomerMetafieldAndMetaobjects($user['id'], 'custom', 'portal_access');
        
        
        $_SESSION['user'] = [
            'id' => $user['id'] ?? '',
            'email' => $user['email'] ?? '',
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'avatar_url' => $user['avatar_url'] ?? null,
            'access' => $access ?? null,
        ];
        error_log("Session: " . print_r($_SESSION['user'], true));
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