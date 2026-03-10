<?php

namespace SW\Source\Engine;

// Ensure PHPMailer is loaded via Composer's autoloader or manual include
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailEngine {
    private $config;

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function Send($recipient, $body = []) {
        // Validation
        if (empty($recipient) || empty($body)) {
            throw new \Exception("Recipient and body are required.");
        }

        $mail = new PHPMailer(true);

        try {
            // --- Server Settings using your Config ---
            $settings = $this->config['email-settings'];

            $mail->isSMTP();
            $mail->Host       = $settings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $settings['username'];
            $mail->Password   = $settings['password'];
            $mail->SMTPSecure = $settings['encryption']; // "tls"
            $mail->Port       = $settings['port'];       // 587

            // --- Recipients ---
            $mail->setFrom($settings['username'], $this->config['app_name']);
            $mail->addAddress($recipient);

            // --- Content ---
            $mail->isHTML(true);
            $mail->Subject = $body['subject'] ?? "Message from " . $this->config['app_name'];
            $mail->Body    = $this->Build($body);
            $mail->AltBody = strip_tags($mail->Body); // Plain text version

            return $mail->send();

        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    private function Build($body) {
        // Here you can wrap your message in a standard HTML template
        $content = $body['message'] ?? "";
        
        return "
            <html>
            <body style='font-family: sans-serif;'>
                <div style='padding: 20px; border: 1px solid #eee;'>
                    $content
                </div>
                <footer style='font-size: 12px; margin-top: 20px; color: #777;'>
                    Sent via {$this->config['app_name']}
                </footer>
            </body>
            </html>
        ";
    }

    public static function LoadTemplate($template, $data = []) {
        // This method can be used to load and render email templates
        $templatePath = ABSPATH . "/resources/templates/{$template}.template.php";
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template not found: {$template}");
        }

        // Extract data to variables for use in the template
        extract($data);

        // Start output buffering to capture the template output
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}