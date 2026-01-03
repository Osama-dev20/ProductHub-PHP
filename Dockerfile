# استخدم صورة PHP مع Apache جاهزة
FROM php:8.2-apache

# تحديد مجلد العمل داخل الحاوية
WORKDIR /var/www/html

# نسخ ملفات المشروع داخل الحاوية
COPY src/ /var/www/html/

# فتح المنفذ 80
EXPOSE 80
