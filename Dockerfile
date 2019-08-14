FROM php:7-apache

ENV APP_PATH /var/www/html

COPY . $APP_PATH
WORKDIR $APP_PATH
EXPOSE 80

# Env variables
ENV APP_ENV dev
ENV APP_SECRET __RANDOM__
ENV APP_DB sqlite
ENV DATABASE_URL __AUTO__
ENV SQLITE_PATH ${APP_PATH}/var/data.db
ENV MYSQL_USER hay
ENV MYSQL_PASSWORD W0wAw3s0m3Passw0d
ENV MYSQL_NAME hay
ENV MYSQL_EXPOSE false

# Intalling dependencies
RUN apt-get update && apt-get upgrade \
    && apt-get install php-mysql php-mbstring php-gd composer \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list \
    && apt-get install yarn \
    && composer install \
    && yarn install \
    && yarn encore ${APP_ENV}

# Process env variables

# App Secret
RUN if [ $APP_SECRET = "__RANDOM__" ] then
    export APP_SECRET=$(head /dev/urandom | tr -dc 'A-Za-z0-9!"#$%&()*+,-./:;<=>?@[\]^_`{|}~' | head -c 32)
fi

# Database configuration
RUN if [ $DATABASE_URL = "__AUTO__" ] then
    if [ $APP_DB = "sqlite" ] then
        export DATABASE_URL="sqlite://${SQLITE_PATH}"
    elif [ $APP_DB = "mysql" ] then
        export DATABASE_URL="mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@127.0.0.1:3306/${MYSQL_NAME}"
    fi
fi

# Configuration
RUN cat > .env << EOF
APP_ENV=$APP_ENV
APP_SECRET=$APP_SECRET

DATABASE_URL="${DATABASE_URL}"
EOF

# Database creation
RUN ${APP_ENV}/bin/console doctrine:database:create && ${APP_ENV}/bin/console doctrine:migrations:migrate
