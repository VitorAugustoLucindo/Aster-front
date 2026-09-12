<?php

class ApiClient {
    private $baseUrl;
    private $token;

    public function __construct() {
        // Use environment variable for production, fallback to local for dev
        $this->baseUrl = getenv('BACKEND_URL') ?: 'http://127.0.0.1:5000';
        $this->token = $_SESSION['aster_jwt'] ?? null;
    }

    /**
     * Generic request method to handle all API calls
     */
    public function request($endpoint, $method = 'GET', $data = null) {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $ch = curl_init($url);
        
        $headers = ['Content-Type: application/json'];
        if ($this->token) {
            $headers[] = "Authorization: Bearer " . $this->token;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Curl error: " . $error);
        }

        curl_close($ch);
        
        $decoded = json_decode($response, true);
        
        if ($httpCode >= 400) {
            throw new Exception($decoded['error']['message'] ?? 'API Error', $httpCode);
        }

        return $decoded;
    }

    public function get($endpoint, $params = []) {
        if (!empty($params)) {
            $endpoint .= '?' . http_build_query($params);
        }
        return $this->request($endpoint, 'GET');
    }

    public function post($endpoint, $data = []) {
        return $this->request($endpoint, 'POST', $data);
    }

    public function patch($endpoint, $data = []) {
        return $this->request($endpoint, 'PATCH', $data);
    }

    public function delete($endpoint) {
        return $this->request($endpoint, 'DELETE');
    }

    public function setToken($token) {
        $this->token = $token;
        $_SESSION['aster_jwt'] = $token;
    }

    public function logout() {
        $this->token = null;
        unset($_SESSION['aster_jwt']);
    }
}
