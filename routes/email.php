<?php

    $Pkg = new \App\Modules\Email\Mailer();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["email"])) {
            $body = [
                "subject" => "Test email",
                "body" => "Yay body nice."
            ];

            if ($Pkg->send($_POST["email"], $body)) {
                die("Sent email successfully");
            }

            die("Unable to send email");
        }

        die("Unable to find the email to send to");
    }

?>

<form action="" method="POST">
    <label for="email">Enter your email:</label>
    <input type="email" id="email" name="email">

    <button action="submit">Submit</button>
</form>