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

## Nginx HTTPS (Untested)
Nginx does not utilise .htaccess files. In this case, the following server block (HTTP) should be fine. However, we have not tested this. If this does not work and you have an alternative - working solution, you can let me know via email: hi@surden.me 
```
# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name domain.com;

    # Set your project root
    root /var/www/domain.com/public;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/domain.com.access.log;
    error_log  /var/log/nginx/domain.com.error.log error;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain.com/privkey.pem;
    ssl_session_cache shared:SSL:10m;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384";
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Robots-Tag none;
    add_header Content-Security-Policy "frame-ancestors 'self'";
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy same-origin;
    # add_header Strict-Transport-Security "max-age=15768000; preload;"; # Uncomment after testing SSL

    # Performance & Uploads
    client_max_body_size 100m;
    client_body_timeout 120s;
    sendfile on; # Changed to 'on' for better performance on standard Linux setups
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
        # Use your verified PHP 8.3 socket path
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        
        fastcgi_index index.php;
        include fastcgi_params;
        
        # Pass file upload limits directly to PHP
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_PROXY ""; # Mitigate httpoxy vulnerability
        
        # Buffers & Timeouts
        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    # Block access to .htaccess, .git, .env, etc.
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

# Disclaimers
This framework is not production ready or is ready for any production releases of software. 
If you are happy to use this framework due to lower overheads and faster - more native execution times, you can, however we are not responsible for any vulnerabilities as this is an educational framework and should not be handling sensitive information. You have been warned. 