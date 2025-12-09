<?php
abstract class BaseController {

    protected function handleSuccessResponse($data, string $successMessage, string $viewPath): void {
        if ($this->isAjaxRequest()) {
            $this->sendJsonSuccess($data, $successMessage);
        } else {
            $this->sendHtmlSuccess($successMessage, $viewPath);
        }
    }

    protected function handleErrorResponse(Exception $e, string $viewPath, int $httpCode = 400): void {
        if ($this->isAjaxRequest()) {
            $this->sendJsonError($e->getMessage(), $httpCode);
        } else {
            $this->sendHtmlError($e->getMessage(), $viewPath);
        }
    }

    protected function isAjaxRequest(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function validatePostRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
    }

    private function sendJsonSuccess($data, string $message): void {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (is_array($data)) {
            $response = array_merge($response, $data);
        } elseif (is_object($data)) {
            $response['data'] = $data;
        }

        echo json_encode($response);
    }

    private function sendJsonError(string $errorMessage, int $httpCode): void {
        http_response_code($httpCode);
        echo json_encode([
            'success' => false,
            'error' => $errorMessage
        ]);
    }

    private function sendHtmlSuccess(string $message, string $viewPath): void {
        echo "<script>window.alert('" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "');</script>";
        include_once __DIR__ . '/../views/' . $viewPath;
    }

    private function sendHtmlError(string $errorMessage, string $viewPath): void {
        echo "<script>window.alert('" . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . "');</script>";
        include_once __DIR__ . '/../views/' . $viewPath;
    }

    protected function handleError(Exception $e): void {
        http_response_code(500);
        echo "<script>window.alert('Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "');</script>";
        include_once __DIR__ . '/../views/errors/500.php';
    }
}
