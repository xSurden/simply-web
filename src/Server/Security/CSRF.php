<?php

namespace SW\Source\Server\Security;

class CSRF {

    public static function Generate(): void {
        if (empty($_SESSION['csrf_token'])) {
            self::Rotate();
        }
    }

    public static function Rotate(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function Insert(): void {
        self::Generate(); 
        $token = $_SESSION['csrf_token'];
        echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function Validate(bool $rotate = false): bool {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            self::ThrowError("CSRF token missing.");
        }

        $isValid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);

        if (!$isValid) {
            self::ThrowError("Invalid CSRF token.");
        }

        if ($rotate) {
            self::Rotate();
        }

        return true;
    }

    private static function ThrowError(string $internalMsg): void {
        $data = [
            "type" => "Error - CSRF",
            "code" => 405,
            "message" => "We failed to validate your CSRF token - operation has been cancelled"
        ];
        \SW\Source\Server\Engine\TemplateEngine::Render("server/message", $data);
        exit();
    }
}