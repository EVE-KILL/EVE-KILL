FROM bitnami/minideb:latest

# Run as root
USER root

# Install PHP
ARG PHP_VERSION="8.2"
RUN \
    apt update && \
    apt upgrade -y && \
    install_packages \
        software-properties-common \
        gettext-base \
        patch \
        wget \
        curl \
        procps \
        default-mysql-client \
    && \
    curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg && \
    sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' && \
    apt update && \
    install_packages \
        php${PHP_VERSION} \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-readline \
        php${PHP_VERSION}-common \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-igbinary \
        php${PHP_VERSION}-apcu \
        php${PHP_VERSION}-imagick \
        php${PHP_VERSION}-yaml \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-mysqlnd \
        php${PHP_VERSION}-mysqli \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-bz2 \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-msgpack \
        php${PHP_VERSION}-intl \
        php${PHP_VERSION}-zstd \
        php${PHP_VERSION}-redis \
        php${PHP_VERSION}-lz4 \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-opcache \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-soap \
        php${PHP_VERSION}-exif \
        php${PHP_VERSION}-xsl \
        php${PHP_VERSION}-gettext \
        php${PHP_VERSION}-cgi \
        php${PHP_VERSION}-dom \
        php${PHP_VERSION}-ftp \
        php${PHP_VERSION}-iconv \
        php${PHP_VERSION}-pdo \
        php${PHP_VERSION}-simplexml \
        php${PHP_VERSION}-tokenizer \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-xmlwriter \
        php${PHP_VERSION}-xmlreader \
        php${PHP_VERSION}-fileinfo \
        php${PHP_VERSION}-uploadprogress \
    && \
    # Symlink the php-fpm${PHP_VERSION} binary to php-fpm
    if [ ! -f /usr/sbin/php-fpm ]; then ln -s /usr/sbin/php-fpm${PHP_VERSION} /usr/sbin/php-fpm; fi && \
    # Cleanup
    rm -rf /tmp/* /src

# Set workdir
WORKDIR /app

# Copy in the module configurations
COPY config/modules/* /etc/php/${PHP_VERSION}/mods-available/

# Copy in production configs
ARG PHP_VERSION="8.2"
COPY config/www.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
COPY config/php-fpm.conf.tmpl /etc/php/${PHP_VERSION}/fpm/php-fpm.conf.tmpl
COPY config/php.ini.tmpl /etc/php/${PHP_VERSION}/fpm/php.ini.tmpl

# Copy in entrypoint
COPY config/php-fpm.sh /entrypoint-fpm.sh
COPY config/entrypoint.sh /entrypoint.sh

# Make entrypoints executable and fix the PHP version number
ARG PHP_VERSION="8.2"
RUN chmod +x /entrypoint-fpm.sh /entrypoint.sh && \
    # Replace PHPVERSION with the variable ${PHP_VERSION}
    sed -i "s/PHPVERSION/${PHP_VERSION}/g" /entrypoint-fpm.sh /etc/php/${PHP_VERSION}/fpm/php-fpm.conf

WORKDIR /app
EXPOSE 9000
ENTRYPOINT [ "/entrypoint.sh" ]
ARG PHP_VERSION="8.2"
ENV PHP_VERSION=${PHP_VERSION}
ARG PHP_MEMORY_LIMIT="768M"
ENV PHP_MEMORY_LIMIT=${PHP_MEMORY_LIMIT}
CMD [ "/entrypoint-fpm.sh" ]
