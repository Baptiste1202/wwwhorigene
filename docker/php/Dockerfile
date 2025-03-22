
FROM core-harbor.pictime-groupe-integ.com/docker-base/symfony-base:8.2

USER root
COPY --chmod=755 scripts/install.sh /usr/local/bin/install.sh
RUN /usr/local/bin/install.sh
COPY config/php.ini /etc/php82/php.ini
COPY config/php-fpm/www.conf /etc/php82/php-fpm.d/www.conf

ARG USER_ID
ARG GROUP_ID
RUN groupmod -g ${GROUP_ID} ftpgrp && usermod -u ${USER_ID} -g ftpgrp web
RUN chmod -R 777 /var/log/supervisor ; \
    chmod -R 777 /var/run/supervisor; \
    chmod -R 777 /var/log/php82; \
    chmod -R 777 /var/run/php; \
    chmod -R 777 /var/lib/nginx; \
    chmod -R 777 /var/log/nginx; \
    chmod -R 777 /var/run/nginx
RUN apk -u add jpegoptim optipng pngquant gifsicle libwebp-tools libavif ffmpeg
USER web

CMD ["/usr/bin/supervisord"]