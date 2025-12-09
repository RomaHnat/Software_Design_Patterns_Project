<?php

class NotFoundException extends Exception {
    public function __construct(string $message = "Resource not found", int $code = 404, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class ValidationException extends Exception {
    public function __construct(string $message = "Validation failed", int $code = 400, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class DuplicateKeyException extends Exception {
    public function __construct(string $message = "Duplicate key violation", int $code = 409, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class InsufficientStockException extends Exception {
    public function __construct(string $message = "Insufficient stock", int $code = 400, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
