<?php
    $config = require_once ABSPATH . "/settings.php";
    $EmailEngine = new \SW\Source\Server\Engine\EmailEngine($config);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $recipient = $_POST["email"] ?? null;
        $email_template_data = [
            "name" => "Simply-Web",
            "description" => "PHP framework, simplified. Built for multi-platform support without relying on external dependencies. Perfect for small projects, learning, and rapid prototyping.",
            "version" => "Beta 0.1",
            "branch" => "Development",
        ];
        $body = [
            "subject" => "Test Email from Simply-Web",
            "message" => $EmailEngine::LoadTemplate("email/test", $email_template_data)
        ];

        try {
            $result = $EmailEngine->Send($recipient, $body);
            if ($result) {
                echo "<p>Email sent successfully to $recipient.</p>";
            } else {
                echo "<p>Failed to send email to $recipient.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    }

?>

<form action="" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Send Test Email</button>
</form>