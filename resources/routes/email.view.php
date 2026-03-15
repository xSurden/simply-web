<?php

    $EmailService = new \SW\Source\Modules\SimplyUtilities\Email();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Use 'message' to match the class logic
        $body = [
            "subject" => "Test email",
            "message" => "This is the body of the email" 
        ];

        // Ensure we check if the post variable exists
        $recipient = $_POST["email_address"] ?? null;

        if ($recipient && $EmailService->Send($recipient, $body)) {
            die("Sent email to " . htmlspecialchars($recipient));
        }
        
        die("Failed to send email.");
    }

?>

<form action="" method="POST">
    <input type="email" name="email_address" placeholder="Your email" required>
    <button type="submit">Click to send email</button>
</form>