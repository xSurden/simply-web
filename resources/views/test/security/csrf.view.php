<?php

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $result = SW\Source\Server\Security\CSRF::Validate();

        if (!$result) {
            $data = [
                "type" => "Error - CSRF",
                "code" => 405,
                "message" => "We failed to validate your CSRF token - operation has been cancelled"
            ];
            SW\Source\Server\Engine\TemplateEngine::Render("server/message", $data);
            exit();
        }

        echo "Received data securely!";
        SW\Source\Server\Security\CSRF::Rotate();
    }

?>

<form action="" method="POST">
    <?= SW\Source\Server\Security\CSRF::Insert(); ?>
    <button type="submit">Update</button>
</form>
