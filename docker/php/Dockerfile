
FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql


# Ajouter la directive ServerName pour éviter l'avertissement
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN    curl -sS https://getcomposer.org/installer | php && \
mv composer.phar /usr/local/bin/composer

# Activer le module Apache pour le rewrite (réécriture d'URL)
RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Copier et remplacer 000-default.conf
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copier et remplacer .htacces
COPY .htacces /var/www/html/public/.htaccess 


COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
