#!/usr/bin/env bash
declare -A params=$6     # Create an associative array
paramsTXT=""
if [ -n "$6" ]; then
   for element in "${!params[@]}"
   do
      paramsTXT="${paramsTXT}
      fastcgi_param ${element} ${params[$element]};"
   done
fi

block="server {
    listen ${3:-80};
    listen ${4:-443} ssl http2;
    server_name $1;
    root \"$2\";

    charset utf-8;

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/$1-error.log error;

    sendfile off;

    client_max_body_size 100m;

    location / {
        index index.html index.php;
        try_files \$uri \$uri/ @rewrite;
        expires 30d;
    }

    ## These locations would be hidden by .htaccess normally
    location ^~ /app/                { return 404; }
    location ^~ /includes/           { return 404; }
    location ^~ /lib/                { return 404; }
    location ^~ /media/downloadable/ { return 404; }
    location ^~ /pkginfo/            { return 404; }
    location ^~ /report/config.xml   { return 404; }
    location ^~ /var/                { return 404; }

    location /var/export/ {
        auth_basic           \"Restricted\";
        auth_basic_user_file htpasswd;
        autoindex            on;
    }

    location  /. {
        return 404;
    }

    location @rewrite {
        rewrite ^(/[a-zA-z0-9]+/)(.*) \$1/index.php/\$2;
    }

    location ~ .php/ { ## Forward paths like /js/index.php/x.js to relevant handler
        rewrite ^(.*.php)/ \$1 last;
    }

    location ~ .php\$ { ## Execute PHP scripts
        if (!-e \$request_filename) {
          rewrite / /index.php last;
        }
        expires        off;
        fastcgi_pass unix:/var/run/php/php$5-fpm.sock;
        #fastcgi_param  HTTPS \$fastcgi_https;
        include        fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  \$document_root\$fastcgi_script_name;
        $paramsTXT
        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }

    ssl_certificate     /etc/nginx/ssl/$1.crt;
    ssl_certificate_key /etc/nginx/ssl/$1.key;
}
"

echo "$block" > "/etc/nginx/sites-available/$1"
ln -fs "/etc/nginx/sites-available/$1" "/etc/nginx/sites-enabled/$1"
