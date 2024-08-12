<?php

class ComprobanteController {
    private $config;

    public function __construct() {
        $this->config = include(__DIR__ . '/../config/afip_config.php');
    }

    public function emitirFactura($data) {
        $url = $this->config['base_url'] . 'facturacion/nuevo';
        return $this->sendRequest($url, $data);
    }

    private function sendRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config['apitoken']
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
?>
