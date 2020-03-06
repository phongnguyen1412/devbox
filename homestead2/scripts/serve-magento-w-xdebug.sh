#!/usr/bin/env bash

echo "Installation Mekongoo Website on nginx (with xdebug)"
block='server {
    listen 80;
    server_name MagentoWithXdebug;
    root "/home/vagrant/Code";

    #charset utf-8;

    location ~ ^/(app/|includes/|pkginfo/|var/|errors/local.xml|lib/|media/downloadable/) { deny all; }
        location ~ /\. { deny all; }
        location / {

        index  index.php;
                try_files $uri $uri/ @rewrite;
        }

        location @rewrite {
           rewrite ^(/[a-zA-z0-9]+/)(.*) $1/index.php/$2;
        }

        location ~ \.php/ { ## Forward paths like /js/index.php/x.js to relevant handler
                rewrite ^(.*.php)/ $1;
        }

    #location = /favicon.ico { access_log off; log_not_found off; }
    #location = /robots.txt  { access_log off; log_not_found off; }

    #access_log off;
    #error_log  /var/log/nginx/homestead.app-error.log error;

    #sendfile off;

    #client_max_body_size 100m;

    location ~ \.php$ {
        if (!-e $request_filename) {
                        rewrite / /index.php last;
                }
                   fastcgi_split_path_info ^(.+\.php)(.*)$;
                   fastcgi_pass  unix:/var/run/php5-fpm.sock;
                   fastcgi_index index.php;
                   include fastcgi_params;
                   fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                   fastcgi_param PATH_INFO $fastcgi_script_name;
                   fastcgi_param  MAGE_RUN_CODE default;
                        fastcgi_param  MAGE_RUN_TYPE store;
                        try_files $uri =404;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
    ssl_certificate     /etc/ssl/certs/ssl-cert-snakeoil.pem;
    ssl_certificate_key /etc/ssl/private/ssl-cert-snakeoil.key;
	rewrite /skin/m/([0-9]+)(/.*\.(js|css)) /devGroupBuy/lib/minify/m.php?f=$2&d=$1 last;

}
'

echo "$block" > "/etc/nginx/sites-available/homestead.app"
ln -fs "/etc/nginx/sites-available/homestead.app" "/etc/nginx/sites-enabled/homestead.app"
service nginx restart
service php5-fpm restart
