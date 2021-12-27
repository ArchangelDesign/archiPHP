FROM archangelraffael/archi-1.0-apache:latest

COPY . /archi
WORKDIR /archi
RUN cp .env.example .env
RUN composer install --no-dev --prefer-dist
