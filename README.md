# Xhprof Bundle

## Introduction
This bundle aims to make life easier to developers who want to profile their application with Xhprof.
It is inspired from this package https://github.com/zim32/symfony-xhprof-bundle for the integration in Symfony's profiler.
This bundle uses the [perftools/php-profiler](https://github.com/perftools/php-profiler) package to run the xhprof profiler and send collected data to [xhgui](https://github.com/perftools/xhgui).

**Note : This bundle can be used in prod environment but for the moment, I recommend to use it only in dev environment.**

## Install

### Step 1: Download the Bundle

Add this repositories section on your composer.json project.

```composer
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:odandb/xhprof-bundle.git"
    }
],
```

And run

```console
composer require --dev odandb/xhprof-bundle
```

### Step 2: Enable the Bundle

```php
// config/bundle.php

return [
    // ...
    Odandb\XhprofBundle\OdandbXhprofBundle::class => ['dev' => true],
];
```

### Step 3 : Add the remaining configuration
You can follow the steps from the [perftools/php-profiler](https://github.com/perftools/php-profiler#usage) package to configure the bundle.

Copy the php_xhgui_config.php.dist file from the vendor/odandb/xhprof-bundle into the root of your project.
Rename it to php_xhgui_config.php and edit it to match your needs.
````shell
cp vendor/odandb/xhprof-bundle/php_xhgui_config.php.dist php_xhgui_config.php
````

### Step 3-bis : If you use docker
Let's say you run a symfony application with docker (nginx, php, mysql) and you want to use xhgui to visualize the results :

**Before :**

![app-nginx-php-mysql.png](docs%2Fimg%2Fapp-nginx-php-mysql.png "App running with nginx / php")

**After :**

![app+xhprof+xhgui.png](docs%2Fimg%2Fapp%2Bxhprof%2Bxhgui.png "App running with nginx / php and xhprof / xhgui")


You can add the following configuration to your docker-compose.yml file.
```yaml
# docker-compose.yml
# If you have an nginx container, you can add the following configuration to it.
nginx:
  # ...
  depends_on:
    # ...
    - php_xhgui
  volumes:
    # ...
    - xhgui_app:/var/www/xhgui
  networks:
    - bridge

## Container for XHGUI
php_xhgui:
  image: xhgui/xhgui:latest
  container_name: php_xhgui
  environment:
    XHGUI_SAVE_HANDLER: mongodb
    XHGUI_MONGO_HOSTNAME: mongo_xhgui
    # generate a random token using : openssl rand -base64 32
    XHGUI_UPLOAD_TOKEN: "CCA4Ymntva2q1ugOFeHNbaDB8YIUA0DL0u7guLhVpiU="
  volumes:
    - xhgui_app:/var/www/xhgui
  networks:
    - bridge

## BDD for XHGUI
mongo:
  image: mongo:latest
  container_name: mongo_xhgui
  networks:
    - bridge

volumes:
  # Shared internal volume for XHGUI app and Nginx.
  xhgui_app:
```

Add the following configuration to your nginx.conf file for **http and/or https**.
```nginx
# nginx.conf
# ...
server {
    # ...
    
    ##-- ONLY FOR XHPROF --##
    ## This block is meant to be used by the Symfony App to upload collected data by the profiler 
    ## to the /xhprof/run/import endpoint (**http &| https**)
    location /xhprof {
        index index.html index.htm index.php;

        alias /srv/xhgui/webroot;

        # try to serve file directly, fallback to index.php
        try_files $uri $uri/ @xhprof;

        location ~ \.php$ {
            try_files $uri =404;
            include /etc/nginx/fastcgi_params;
            fastcgi_pass    php_xhgui:9000;
            fastcgi_index   index.php;
            # Chemin vers le fichier index.php dans le container php_xhgui
            fastcgi_param SCRIPT_FILENAME /var/www/xhgui/webroot/index.php;
            client_body_buffer_size 1M;
        }
    }

    location @xhprof {
        rewrite /xhprof/(.*)$ /xhprof/index.php?/$1 last;
    }
    ##-- ONLY FOR XHPROF --##
}
```

Add the following configuration to your Dockerfile file for **php**.
```dockerfile
# Dockerfile
# ...
##-- ONLY FOR XHPROF --##
RUN git clone "https://github.com/longxinH/xhprof.git" xhprof-ext \
    && cd xhprof-ext/extension \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && cd ../.. \
    && rm -rf xhprof-ext \
    && docker-php-ext-enable xhprof
##-- ONLY FOR XHPROF --##
```

## Usage
### Set environment variables
You can set the following environment variables to configure the bundle :

```dotenv
###> XHGUI PROFILER COLLECTOR ###
## switch to true to enable the profiler, false otherwise
XHGUI_PROFILER_ENABLE=true
XHGUI_PROFILER_IMPORT_URL=http://<nginx_internal_container_name>/xhprof/run/import

## the token must be the same as the one defined in the XHGUI_UPLOAD_TOKEN environment variable of the php_xhgui container
## generate a random token using : openssl rand -base64 32
XHGUI_UPLOAD_TOKEN="CCA4Ymntva2q1ugOFeHNbaDB8YIUA0DL0u7guLhVpiU="
###< XHGUI PROFILER COLLECTOR ###
```

### Profile your application
You can profile your application by enabling the profiler (see section above) and browse your application.
The profiler is triggered before the controller execution and at the ResponseKernelEvent.
If you use the Symfony profiler feature, you should see the Xhprof section in the toolbar :


![xhprof-profiler-toolbar.png](docs%2Fimg%2Fxhprof-profiler-toolbar.png)

You can also see the collected data in the profiler panel :

![xhprof-profiler-panel.png](docs%2Fimg%2Fxhprof-profiler-panel.png)

To visualize the collected data, you can go to the XHGUI interface (see section above) and click on a row to go to its details.

![xhgui_interface.png](docs%2Fimg%2Fxhgui_interface.png "XHGUI interface")

## More to come

- [ ] More tests
- [ ] Add more configuration options (only uploader is supported for now)
- [ ] Add a Symfony recipe to copy the default configuration file
