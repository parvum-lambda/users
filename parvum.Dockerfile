FROM ubuntu:22.04

ARG WWWGROUP=1000
ARG POSTGRES_VERSION=14

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV APP_NAME=Users
ENV APP_ENV=local
ENV APP_DEBUG=true
ENV APP_URL=http://localhost
ENV APP_PORT=8000

ENV LOG_CHANNEL=stack
ENV LOG_DEPRECATIONS_CHANNEL=null
ENV LOG_LEVEL=debug

ENV DB_CONNECTION=pgsql
ENV DB_HOST=pgsql
ENV DB_PORT=5432
ENV DB_DATABASE=parvum_users
ENV DB_USERNAME=users
ENV DB_PASSWORD=password

ENV BROADCAST_DRIVER=log
ENV CACHE_DRIVER=file
ENV FILESYSTEM_DISK=local
ENV QUEUE_CONNECTION=sync
ENV SESSION_DRIVER=file
ENV SESSION_LIFETIME=120

ENV MEMCACHED_HOST=127.0.0.1

ENV KAFKA_BROKERS=parvum.events:9092

ENV DYNAMODB_LOCAL=true
ENV DYNAMODB_CONNECTION=local
ENV DYNAMODB_LOCAL_ENDPOINT=dynamodb:8000

ENV XDEBUG_MODE=off
ENV XDEBUG_CONFIG="client_host=host.docker.internal"

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 dnsutils \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /usr/share/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php8.2-cli php8.2-dev \
       php8.2-pgsql php8.2-sqlite3 php8.2-gd \
       php8.2-curl \
       php8.2-imap php8.2-mysql php8.2-mbstring \
       php8.2-xml php8.2-zip php8.2-bcmath php8.2-soap \
       php8.2-intl php8.2-readline \
       php8.2-ldap \
       php8.2-rdkafka \
       php8.2-msgpack php8.2-igbinary php8.2-redis php8.2-swoole \
       php8.2-memcached php8.2-pcov php8.2-xdebug \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /usr/share/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y mysql-client \
    && apt-get install -y postgresql-client-$POSTGRES_VERSION \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.2

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY .parvum/php/8.2/start-container /usr/local/bin/start-container
COPY .parvum/php/8.2/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .parvum/php/8.2/php.ini /etc/php/8.2/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

WORKDIR /var/www/html

COPY . .

RUN composer install
RUN composer run post-autoload-dump

RUN echo APP_KEY=base64:$(openssl rand -base64 32) > .env

EXPOSE 8000

ENTRYPOINT ["start-container"]
