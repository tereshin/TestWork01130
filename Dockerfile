FROM wordpress:6.4-php8.2-apache

# Установка дополнительных PHP расширений
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Копирование конфигурации Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Включение mod_rewrite для WordPress
RUN a2enmod rewrite

# Настройка безопасности Apache
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf

# Установка правильных прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Копирование WordPress файлов
COPY . /var/www/html/

# Создание скрипта для генерации wp-config.php
RUN echo '#!/bin/bash' > /usr/local/bin/generate-wp-config.sh \
    && echo 'cat > /var/www/html/wp-config.php << EOF' >> /usr/local/bin/generate-wp-config.sh \
    && echo '<?php' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("DB_NAME", getenv("WORDPRESS_DB_NAME"));' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("DB_USER", getenv("WORDPRESS_DB_USER"));' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("DB_PASSWORD", getenv("WORDPRESS_DB_PASSWORD"));' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("DB_HOST", getenv("WORDPRESS_DB_HOST"));' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("DB_CHARSET", "utf8mb4");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("DB_COLLATE", "");' >> /usr/local/bin/generate-wp-config.sh \
    && echo '\$table_prefix = "wp_";' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("WP_DEBUG", getenv("WORDPRESS_DEBUG"));' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("WP_DEBUG_LOG", true);' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("WP_DEBUG_DISPLAY", false);' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("AUTH_KEY", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("SECURE_AUTH_KEY", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("LOGGED_IN_KEY", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("NONCE_KEY", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("AUTH_SALT", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("SECURE_AUTH_SALT", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("LOGGED_IN_SALT", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'define("NONCE_SALT", "'$(openssl rand -base64 32)'");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'if (!defined("ABSPATH")) define("ABSPATH", __DIR__ . "/");' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'require_once ABSPATH . "wp-settings.php";' >> /usr/local/bin/generate-wp-config.sh \
    && echo 'EOF' >> /usr/local/bin/generate-wp-config.sh \
    && chmod +x /usr/local/bin/generate-wp-config.sh

# Создание entrypoint скрипта
RUN echo '#!/bin/bash' > /usr/local/bin/docker-entrypoint.sh \
    && echo 'set -e' >> /usr/local/bin/docker-entrypoint.sh \
    && echo '# Генерация wp-config.php если его нет' >> /usr/local/bin/docker-entrypoint.sh \
    && echo 'if [ ! -f /var/www/html/wp-config.php ]; then' >> /usr/local/bin/docker-entrypoint.sh \
    && echo '  /usr/local/bin/generate-wp-config.sh' >> /usr/local/bin/docker-entrypoint.sh \
    && echo 'fi' >> /usr/local/bin/docker-entrypoint.sh \
    && echo 'exec "$@"' >> /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Настройка entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
