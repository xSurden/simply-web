<?php

    header(
        "Content-Security-Policy: " .
        "default-src 'self'; " .
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com https://cdn.tailwindcss.com https://cdn.jsdelivr.net; " .
        "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com; " .
        "frame-src https://www.google.com; " . 
        "font-src 'self' https://fonts.gstatic.com; " .
        "img-src 'self' data: https://www.gstatic.com; " .
        "connect-src 'self' https://cdn.jsdelivr.net; " .
        "worker-src 'self' blob:; " .
        "child-src 'self' blob:; " . 
        "object-src 'none';"
    );

?>