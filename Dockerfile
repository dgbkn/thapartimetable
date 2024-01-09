# Use an official PHP runtime as a parent image
FROM php:7.4-apache


# Set the COMPOSER_ALLOW_SUPERUSER environment variable
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your PHP application code into the container
COPY [^composer.lock]* ./

# Install PHP extensions and other dependencies
# Install PHP extensions and other dependencies
RUN apt-get update && \
    apt-get install -y libpng-dev libzip-dev && \
    docker-php-ext-install pdo pdo_mysql gd zip

# Enable custom PHP extensions
RUN echo "extension=gd" > /usr/local/etc/php/conf.d/docker-php-ext-gd.ini && \
    echo "extension=pdo_mysql" > /usr/local/etc/php/conf.d/docker-php-ext-pdo_mysql.ini && \
    echo "extension=sodium" > /usr/local/etc/php/conf.d/docker-php-ext-sodium.ini && \
    echo "extension=zip" > /usr/local/etc/php/conf.d/docker-php-ext-zip.ini


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the composer.json file to the container
COPY composer.json ./

RUN chmod -R 777 ./

# Install Composer 
# Install Composer dependencies
RUN composer update --no-interaction


# Change the Apache port to 8080
RUN sed -ri -e 's/80/5657/g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's/Listen 80/Listen 5657/g' /etc/apache2/ports.conf

# Expose the new port Apache listens on
EXPOSE 5657

# Start Apache when the container runs
CMD ["apache2-foreground"]
