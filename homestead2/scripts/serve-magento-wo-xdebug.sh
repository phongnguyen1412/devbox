#!/usr/bin/env bash

echo "Installation Mekongoo Website on nginx (without xdebug)"
block='server {
    listen       88;
    server_name  MagentoWithoutXdebug;
	root   "/home/vagrant/Code";
	
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
	ssl_certificate     /etc/ssl/certs/ssl-cert-snakeoil.pem;
    ssl_certificate_key /etc/ssl/private/ssl-cert-snakeoil.key;

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
	location ~ \.php$
	{
		if (!-e $request_filename) {
			rewrite / /index.php last;
		}
		   fastcgi_split_path_info ^(.+\.php)(.*)$;
		   fastcgi_pass 127.0.0.1:9000;
		   fastcgi_index index.php;
		   include fastcgi_params;
		   fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		   fastcgi_param PATH_INFO $fastcgi_script_name;
		   fastcgi_param  MAGE_RUN_CODE default; 
			fastcgi_param  MAGE_RUN_TYPE store;
			try_files $uri =404;
			fastcgi_read_timeout 3600;
		}

        # deny access to .htaccess files, if Apaches document root
        # concurs with nginx s one
        location ~ /\.ht {
            deny  all;
        }
    }
'

echo "$block" > "/etc/nginx/sites-available/mekongoo"
ln -fs "/etc/nginx/sites-available/mekongoo" "/etc/nginx/sites-enabled/mekongoo"
service nginx restart
service php5-fpm restart
