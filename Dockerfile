FROM php:apache
WORKDIR /var/www/switchconfig

# variables
ENV WEBAPP_ROOT /var/www/switchconfig
ENV APACHE_DOCUMENT_ROOT ${WEBAPP_ROOT}

# set up apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    a2enmod rewrite

# install necessary PHP extensions
RUN apt-get update && apt install -y libssh2-1-dev libssh2-1 \
&& pecl install ssh2 \
&& docker-php-ext-enable ssh2

# copy web app files
COPY . ${WEBAPP_ROOT}
RUN if [ ! -f "$WEBAPP_ROOT/config.php" ]; then echo 'config.php missing - please create it from config.php.example!'; exit 1; fi

# start apache
CMD apache2-foreground
