FROM dunglas/frankenphp:latest

# --- system dependencies ---
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# --- PHP extensions ---
RUN docker-php-ext-install \
    intl \
    pdo_pgsql \
    zip

# --- Redis extension ---
RUN pecl install redis \
    && docker-php-ext-enable redis

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- non-root user (host-compatible) ---
ARG UID=1000
ARG GID=1000
RUN groupadd -g ${GID} app \
 && useradd -m -u ${UID} -g ${GID} -s /bin/bash app

WORKDIR /app

# katalogi na cache (żeby nic nie próbowało pisać do /root)
RUN mkdir -p /home/app/.composer /home/app/.cache /app/var \
 && chown -R app:app /home/app /app/var

ENV COMPOSER_HOME=/home/app/.composer
ENV XDG_CACHE_HOME=/home/app/.cache

USER app
