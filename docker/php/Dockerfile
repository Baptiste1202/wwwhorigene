FROM php:8.3-apache

# 1) Extensions PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# 2) Outils
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN apt-get update && apt-get install -y --no-install-recommends zip \
    && rm -rf /var/lib/apt/lists/*

# 3) Apache
RUN a2enmod rewrite
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2dissite 000-default || true && a2ensite 000-default

COPY docker/php/config/php.ini /usr/local/etc/php/conf.d/custom-limits.ini

# 4) Copier le code Symfony
COPY dev/ /var/www/html/
WORKDIR /var/www/html
EXPOSE 80
CMD ["apache2-foreground"]
