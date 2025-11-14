# docker/php/Dockerfile
FROM php:8.3-apache

# 1) Extensions PHP nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 2) Outils (facultatif mais tu les avais)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# zip pour la commande shell "zip"
RUN apt-get update && apt-get install -y --no-install-recommends zip \
    && rm -rf /var/lib/apt/lists/*

# 3) Modules Apache
RUN a2enmod rewrite

# 4) VHost (le fichier est dans le même dossier que ce Dockerfile)
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2dissite 000-default || true && a2ensite 000-default

# 5) Code monté par le volume compose (./dev -> /var/www/html)
WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
