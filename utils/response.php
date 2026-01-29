<?php
// utils/response.php - Helper untuk JSON response

class Response {
    
    // Response sukses
    public function success($success = true, $message = "", $data = null) {
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }
    
    // Response error
    public function error($message = "Terjadi kesalahan", $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'error_code' => $code
        ], JSON_PRETTY_PRINT);
        exit();
    }
    
    // Validasi method request
    public function validateMethod($expected_method) {
        if ($_SERVER['REQUEST_METHOD'] !== $expected_method) {
            $this->error("Method tidak diizinkan. Gunakan $expected_method", 405);
        }
    }
    
    // Validasi required fields
    public function validateRequired($data, $required_fields) {
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $this->error("Field '$field' harus diisi!");
            }
        }
        return true;
    }
}
?>