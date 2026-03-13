<?php

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        // Validate the CSRF token
        // Appending true will run the rotate function
        SW\Source\Server\Security\CSRF::Validate(true);

        // if passes do stuff
        echo \SW\Source\Server\Utilities\Text::DisplayText("Received action securely!");
    }

?>

<h1>Test CSRF module</h1>
<p>
    It should work if you try to submit things without being valid.
</p>
<form action="" method="POST">
    <?= SW\Source\Server\Security\CSRF::Insert(); ?>
    <button type="submit">Update</button>
</form>
