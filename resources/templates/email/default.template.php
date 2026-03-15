<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $app_name; ?></title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee;">
        <h2 style="color: #4A90E2;"><?php echo $app_name; ?></h2>
        <p><?php echo nl2br(htmlspecialchars($content)); ?></p>
        <?php if (!empty($extra)): ?>
            <div style="background: #f9f9f9; padding: 10px; margin-top: 10px;">
                <?php print_r($extra); ?>
            </div>
        <?php endif; ?>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #888;">This is an automated message from <?php echo $app_name; ?>.</p>
    </div>
</body>
</html>