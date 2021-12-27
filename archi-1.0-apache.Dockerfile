FROM php:7.4-apache-buster

RUN apt-get update -yqq \
    && apt-get -u upgrade -y --assume-no \
    && apt-get install -yqq --no-install-recommends \
    git \
    zip \
    unzip \
    libicu-dev \
    libjpeg-dev \
    libfreetype6-dev \
    g++ \
    libpng-dev \
    libxml2-dev \
    zlib1g-dev \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/bin/composer
RUN mkdir /archi && chown -R :www-data /archi
RUN touch /etc/apache2/sites-enabled/archi.conf
RUN echo "<VirtualHost *:80>\n\
              DocumentRoot /archi/public\n\
              <Directory /archi/public/>\n\
                  Options FollowSymLinks\n\
                  AllowOverride All\n\
                  Require all granted\n\
              </Directory>\n\
              </VirtualHost>\n" >/etc/apache2/sites-enabled/archi.conf
RUN rm /etc/apache2/sites-enabled/000-default.conf
