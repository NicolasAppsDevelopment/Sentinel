FROM php:8.2-fpm
ENV WORKDIR=/var/www
ENV GIT_USER_EMAIL="you@example.com"
ENV GIT_USER_NAME="Your Name"
RUN apt-get update && apt-get install -y \
    git \
    sudo \
    supervisor \
    mariadb-client \
    zip \
    libzip-dev \
    ssmtp \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql

RUN apt-get update && apt-get install -y ssmtp
COPY ssmtp.conf /etc/ssmtp/ssmtp.conf

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
CMD ["/usr/bin/supervisord"]

WORKDIR ${WORKDIR}

COPY ./confphp.ini /usr/local/etc/php/conf.d/confphp.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && git config --global user.email ${GIT_USER_EMAIL} \
    && git config --global user.name ${GIT_USER_NAME}

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT [ "entrypoint.sh" ]
CMD ["php-fpm"]
