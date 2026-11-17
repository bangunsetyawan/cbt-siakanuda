# CBT SIAKANUDA — PHP 8.4-FPM untuk smknuda2
# Build: docker compose --env-file .env -f docker/docker-compose.yml build
FROM php:8.4-fpm

ENV TZ=Asia/Jakarta

# Dependensi build untuk ekstensi PHP yang dibutuhkan CI4 + CBT:
# intl (wajib CI4), mbstring (wajib CI4), mysqli (MariaDB), gd (kompres/resize gambar soal)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libonig-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" intl mbstring mysqli gd opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# OPcache: aktif, validate_timestamps=1 agar deploy kode baru langsung kebaca
# (tanpa restart container).
RUN { \
      echo 'opcache.enable=1'; \
      echo 'opcache.memory_consumption=128'; \
      echo 'opcache.interned_strings_buffer=8'; \
      echo 'opcache.max_accelerated_files=10000'; \
      echo 'opcache.validate_timestamps=1'; \
      echo 'opcache.revalidate_freq=2'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Zona waktu container = WIB
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var/www/html

CMD ["php-fpm"]
