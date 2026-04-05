# Simply Web | Recoded ([View Site](https://simplyweb.surden.me))
A hobby PHP framework. Build advanced PHP small to mid-sized web applications quicker than before,
with less overhead requirements, built-in useful features and simple near native syntaxes. 

## Sponsered by ABRHosting.com
Use code **simplyweb** for 30% off on ABR's VPS ranges (Ryzen 9 and Xeon Eco)
[![ABRHosting Website Link](https://abrhosting.com/assets/img/logo%20(1).png)](https://abrhosting.com)

# Why use Simply Web?
If you wish to run a PHP web application that does not require massive framework such as Laravel - for a small to mid-sized project, Simply Web can get you started. Want to learn how PHP works? Use it. We have added useful and basic functionalities to be called right away, vanilla PHP syntaxes and much more. 

# Installing and setting up Simply Web
Once you have cloned/downloaded the repository, you MUST do the following:
1. Upload the project to the web root.
2. Change the web root to /public
3. Run this composer command: ``composer install --no-dev --optimize-autoloader``
4. Done! Visit your new website locally or while in production

This will install all the required library with composer.

## Nginx specific config
```
server {
    listen 80;
    listen [::]:80;
    server_name domain.com;
    root /var/www/domain.com/public;

    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    access_log /var/log/nginx/domain.com.access.log;
    error_log  /var/log/nginx/domain.com.error.log error;
}
```

# Requirements
- Composer
- Web Server (Apache2 or Nginx or others)
- PHP 8.0 and higher
- Document Root change
- .htaccess capabilities (From Apache2) Nginx will also work if you have configured the server block properly (attached). 

# Disclaimers
This framework is super simple, while not on the same level as Laravel, it is quite educational to get a deeper understanding of how PHP works, with database handling and much more. Feel free to look around, edit anything you like and make your own.

We are not responsible for any data leaks or damages as this is not production ready.