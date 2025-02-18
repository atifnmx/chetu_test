## Use an official PHP:8.1-fpm using from our ECR repo.
FROM public.ecr.aws/l7e7u5w1/php

MAINTAINER Mukteshwars@chetu.com

# Install required extensions
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpq-dev && \
    docker-php-ext-install gd pdo pdo_mysql zip opcache 
    
# Install Composer image using from our ECR repo.
COPY --from=public.ecr.aws/l7e7u5w1/composer /usr/bin/composer /usr/bin/composer

# Install Nginx
RUN apt-get install -y --no-install-recommends nginx

# Copy Nginx configuration
COPY nginx-site.conf /etc/nginx/sites-available/default

# Copy application code
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

#RUN mkdir /var/www/html/storage/logs
RUN chmod -R 777 /var/www/html/storage
RUN composer update
# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM
CMD service nginx start && php-fpm
