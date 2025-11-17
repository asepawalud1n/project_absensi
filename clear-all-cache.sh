#!/bin/bash

echo "========================================="
echo "CLEARING ALL CACHES - Dashboard Beranda"
echo "========================================="
echo ""

echo "1. Clearing application cache..."
php artisan cache:clear

echo "2. Clearing config cache..."
php artisan config:clear

echo "3. Clearing route cache..."
php artisan route:clear

echo "4. Clearing view cache..."
php artisan view:clear

echo "5. Clearing event cache..."
php artisan event:clear

echo "6. Clearing compiled classes..."
php artisan clear-compiled

echo "7. Clearing optimize cache..."
php artisan optimize:clear

echo ""
echo "========================================="
echo "ALL CACHES CLEARED SUCCESSFULLY!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Restart your development server (if using php artisan serve)"
echo "2. Hard refresh browser (Ctrl + Shift + R or Cmd + Shift + R)"
echo "3. Clear browser cache completely"
echo "4. Open dashboard in incognito/private mode to test"
echo ""
