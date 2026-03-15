<?php 
    $Captcha = new \SW\Source\Modules\SimplyUtilities\Recaptcha\Google_Recaptcha(); 
?>

<form action="/submit" method="POST">
    <?= $Captcha->Loadv2(); ?>
    <button type="submit">Submit</button>
</form> 