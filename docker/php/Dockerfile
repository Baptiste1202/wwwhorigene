# Dockerfile pour Symfony + Apache + PHP 8.3 sur Render
FROM php:8.3-apache

# --------------------------
# 1) Installer outils nécessaires
# --------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# --------------------------
# 2) Extensions PHP
# --------------------------
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# --------------------------
# 3) Apache
# --------------------------
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# --------------------------
# 4) Installer Composer et Symfony CLI
# --------------------------
RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# --------------------------
# 5) Copier le VHost Apache
# --------------------------
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2dissite 000-default || true && a2ensite 000-default

# --------------------------
# 6) Copier le code Symfony
# --------------------------
COPY dev/ /var/www/html/

WORKDIR /var/www/html

# --------------------------
# 7) Installer les dépendances PHP
# --------------------------
RUN composer install --no-dev --optimize-autoloader

# --------------------------
# 8) Exposer le port
# --------------------------
EXPOSE 80

# --------------------------
# 9) Démarrer Apache
# --------------------------
CMD ["apache2-foreground"]
