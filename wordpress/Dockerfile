FROM wordpress:latest

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    wget \
    less \
    default-mysql-client \
    vim \
    nano \
    sudo \
    subversion \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo_mysql

    # Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp

# Install Composer and PHPUnit (optional but helpful)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    wget https://phar.phpunit.de/phpunit-9.phar && \
    chmod +x phpunit-9.phar && \
    mv phpunit-9.phar /usr/local/bin/phpunit
