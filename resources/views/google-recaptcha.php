<?php
    $recaptcha = new \App\Blueprint\Security\ReCaptcha\Google();
?>

<form action="submit.php" method="POST">
    <label>Email Address</label>
    <input type="email" name="email" required>

    <?php echo $recaptcha->loadV2(); ?>

    <button type="submit">Sign In</button>
</form>