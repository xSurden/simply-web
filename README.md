# Simply Web | Recoded ([View Demo Site](https://simplyweb.surden.me))
A simple and lightweight PHP framework - built for hobbyists, small to mid sized projects and for educational purposes. 

## Sponsered by ABRHosting.com
Use code **simplyweb** for 30% off on ABR's VPS ranges (Ryzen 9 and Xeon Eco)
[![ABRHosting Website Link](https://abrhosting.com/assets/img/logo%20(1).png)](https://abrhosting.com)

# Requirements
- Composer
- Web Server (Apache2 or Nginx or others)
- PHP 8.0 and higher
- Document Root change
- .htaccess capabilities (From Apache2) Nginx will also work if you have configured the server block properly (attached). 

# Installing and setting up Simply Web
Once you have cloned/downloaded the repository, you MUST do the following:
1. Upload the project to the web root.
2. Change the web root to /public
3. Run this composer command to install the required libraries: ``composer install --no-dev --optimize-autoloader``
4. Adding tasks to Crontab:
```
# Run the following and add to the end of the crontab file:
crontab -e

# Add the following to the end of the file
# If it is different path, update the path: /var/www/html/ to your desired path
* * * * * php /var/www/html/server/scripts/run_cron.php >> /dev/null 2>&1
```
5. Done! Visit your new website locally or while in production

## Apache2 + .htaccess (Easy)
This is the main way we run Simply Web. Simply create the site normally and Apache2 will handle the rest.

## Nginx (Untested)
Nginx does not utilise .htaccess files. In this case, the following server block (HTTP) should be fine. However, we have not tested this. If this does not work and you have an alternative - working solution, you can let me know via email: hi@surden.me 
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

# Disclaimers
This framework is not production ready or is ready for any production releases of software. 
If you are happy to use this framework due to lower overheads and faster - more native execution times, you can, however we are not responsible for any vulnerabilities as this is an educational framework and should not be handling sensitive information. You have been warned. 