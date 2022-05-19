FROM php:7-apache

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY start-apache.sh /usr/local/bin
RUN chmod a+x /usr/local/bin/start-apache.sh
RUN a2enmod rewrite
RUN apt-get update \
    && apt-get install -y --no-install-recommends  libpq-dev libpq5 \
    && docker-php-ext-install pgsql \
    && apt-get purge -y --auto-remove libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Copy application source
COPY . /var/www/
RUN chown -R www-data:www-data /var/www
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

CMD ["start-apache.sh"]