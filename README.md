# SimplyWeb PHP Framework
Fast and lightweight PHP framework built to run with minimal overhead and lower dependencies on most platforms. 

## Sponsered by ABRHosting.Com
Use code **simplyweb** for 30% off on ABR's VPS ranges (Ryzen 9 and Xeon Eco)
[![ABRHosting Website Link](https://abrhosting.com/assets/img/logo%20(1).png)](https://abrhosting.com)

# Requirements
- Composer
- Web Server (Apache2 or Nginx or others)
- PHP 8.0 and higher
- Document Root change
- .htaccess capabilities (From Apache2) Nginx will also work if you have configured the server block properly (attached). 

# Disclaimers
This framework is not production ready or is ready for any production releases of software. 
If you are happy to use this framework due to lower overheads and faster - more native execution times, you can, however we are not responsible for any vulnerabilities as this is an educational framework and should not be handling sensitive information. You have been warned. 

# Setup and Installation
1. Open terminal in the folder you like to clone the repository
2. Clone the github repository via ``git clone https://github.com/xSurden/simply-web``
3. Change folder ``cd simply-web``. This may be different on how you cloned the files. 
4. Install required libraries via ``composer install --no-dev --optimize-autoloader`` inside of the project root folder.
5. Add to contab (recommended - Linux only): ``* * * * * php /webserver_path/server/scripts/run_cron.php >> /dev/null 2>&1``
6. (Development Only) Start local PHP server with ``php -S localhost:80 -t ./public``

# Production (via Linux / Webhosting Panel such as HestiaCP)
At of current, I do not believe that there is any possible way of hosting this on cPanel as changing the root directory is required. 

## HestiaCP
Follow setup and installation step above from step 1 to 5. To run commands, you should use SSH/bash capabilities. 

## If you are using Nginx (Untested)
```
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

    root /var/www/domain.com/public;
    index index.php index.html;

    access_log /var/log/nginx/domain.com.access.log;
    error_log  /var/log/nginx/domain.com.error.log error;

    ssl_certificate /etc/letsencrypt/live/domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain.com/privkey.pem;
    ssl_session_cache shared:SSL:10m;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384";
    ssl_prefer_server_ciphers on;

    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Robots-Tag none;
    add_header Content-Security-Policy "frame-ancestors 'self'";
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy same-origin;

    client_max_body_size 100m;
    client_body_timeout 120s;
    sendfile on; # Changed to 'on' for better performance on standard Linux setups
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        
        fastcgi_index index.php;
        include fastcgi_params;
        
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_PROXY ""; # Mitigate httpoxy vulnerability
        
        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```