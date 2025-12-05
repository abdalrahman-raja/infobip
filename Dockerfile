# استخدم صورة PHP رسمية كقاعدة
FROM php:8.2-fpm-alpine

# تثبيت الاعتمادات اللازمة
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    && docker-php-ext-install pdo_mysql zip gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# تثبيت مكتبات PHP
RUN composer install --no-dev --optimize-autoloader

# تعيين صلاحيات الملفات
RUN chown -R www-data:www-data /var/www/html

# تشغيل خادم PHP المدمج للاستماع على منفذ 8080
# Render Web Services تتوقع أن يستمع التطبيق على المنفذ المحدد في متغير البيئة PORT
# سنستخدم 8080 كمنفذ افتراضي
CMD ["php", "-S", "0.0.0.0:8080", "webhook.php"]
