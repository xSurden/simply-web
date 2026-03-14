<?php

namespace SW\Source\Modules\SimplyUtilities;

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
            $settings = $this->config['email-settings'];

            $mail->isSMTP();
            $mail->Host       = $settings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $settings['username'];
            $mail->Password   = $settings['password'];
            $mail->SMTPSecure = $settings['encryption'];
            $mail->Port       = $settings['port'];   

            $mail->setFrom($settings['username'], $this->config['app_name']);
            $mail->addAddress($recipient);

            $mail->isHTML(true);
            $mail->Subject = $body['subject'] ?? "Message from " . $this->config['app_name'];
            $mail->Body    = $this->Build($body);
            $mail->AltBody = strip_tags($mail->Body);

            return $mail->send();

        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    private function Build($body) {
        $templateName = $body['template'] ?? 'default';
        
        return self::LoadTemplate($templateName, [
            'content'  => $body['message'] ?? '',
            'app_name' => $this->config['app_name'],
            'extra'    => $body['extra_data'] ?? []
        ]);
    }

    public static function LoadTemplate($template, $data = []) {
        $templatePath = ABSPATH . "/resources/templates/{$template}.template.php";
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template not found: {$template}");
        }

        extract($data);

        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}