<?php

class NodeRedClient {
    private $baseUrl;
    private $token;

    public function __construct($baseUrl = URL_NODE_RED, $token = TOKEN_NODE_RED) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
    }

    public function request($method, $endpoint, array $data = []) {
        if (!function_exists('curl_init')) {
            return ['success' => false, 'error' => 'La extensión cURL de PHP no está habilitada.'];
        }

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $curl = curl_init($url);
        $payload = !empty($data) ? json_encode($data) : null;

        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ]
        ]);

        if ($payload !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }

        $body = curl_exec($curl);
        $curlError = curl_error($curl);
        $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($body === false) {
            return ['success' => false, 'error' => 'No se pudo conectar con Node-RED: ' . $curlError];
        }

        $response = json_decode($body, true);
        if ($statusCode < 200 || $statusCode >= 300) {
            $error = is_array($response) ? ($response['message'] ?? $response['error'] ?? null) : null;
            return ['success' => false, 'error' => $error ?: 'Node-RED respondió con código HTTP ' . $statusCode . '.', 'status_code' => $statusCode];
        }

        return ['success' => true, 'data' => $response ?? $body, 'status_code' => $statusCode];
    }
}