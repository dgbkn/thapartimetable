# Use an official PHP runtime as a parent image
FROM php:7.4-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Copy your PHP application code into the container
COPY . .

# Install PHP extensions and other dependencies
RUN apt-get update && \
    apt-get install -y libpng-dev && \
    docker-php-ext-install pdo pdo_mysql gd

# Change the Apache port to 8080
RUN sed -ri -e 's/80/5657/g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's/Listen 80/Listen 5657/g' /etc/apache2/ports.conf

# Expose the new port Apache listens on
EXPOSE 5657

# Start Apache when the container runs
CMD ["apache2-foreground"]
