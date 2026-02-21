# Setup and Testing Guide

## Quick Start

### 1. Local Testing (Using PHP Built-in Server)

```bash
cd /path/to/delasof2026_php/public
php -S localhost:8000
```

Then access: `http://localhost:8000/`

### 2. Admin Login

- **URL**: `http://localhost:8000/admin`
- **Username**: `admin`
- **Password**: `sofaraway`

### 3. Upload Sample CSV

1. Go to Admin Panel → CSV Tab
2. Click "เลือกไฟล์ CSV"
3. Select a CSV file with product data
4. The CSV should have the following columns:

```
product_id,product_url,product_name,product_price,product_discounted,product_min_price,product_max_price,product_sold,product_discount_percentage,product_discount_text,product_shop_id,product_shop_name,product_shop_location,product_rating,product_review_count,...
```

### 4. Test Features

**Homepage**
- Visit `http://localhost:8000/`
- Search for products
- Filter by sort options
- Navigate through pagination

**Product Detail**
- Click on any product
- View product details
- Add to wishlist
- Click "ดูสินค้า" to test URL cloaking

**Admin Panel**
- Configure site settings
- Manage CSV files
- Manage categories

**Wishlist**
- Visit `http://localhost:8000/wishlist`
- View saved products

## File Structure Verification

```
delasof2026_php/
├── public/
│   ├── index.php ✓
│   ├── .htaccess ✓
│   └── assets/
│       ├── css/ ✓
│       ├── js/ ✓
│       └── images/ ✓
├── src/
│   ├── api/ ✓
│   ├── pages/ ✓
│   ├── components/ ✓
│   └── lib/ ✓
├── storage/
│   ├── config.json (auto-created)
│   ├── csv/ ✓
│   └── logs/ ✓
└── README.md ✓
```

## Configuration

### Default Config (storage/config.json)

```json
{
  "siteName": "ThaiDeals",
  "dataSource": "csv",
  "categories": [
    "สินค้าแนะนำ",
    "ดีลเด็ด",
    "ของใช้ในบ้าน",
    "แฟชั่น",
    "อิเล็กทรอนิกส์"
  ],
  "keywords": [
    "โปรโมชั่น",
    "ลดราคา",
    "สินค้ายอดนิยม",
    "ของใช้ในบ้าน",
    "แฟชั่น"
  ],
  "enableFlashSale": true,
  "enableAiReviews": false,
  "defaultCurrency": "THB",
  "cloakingToken": "QlpXZyCqMylKUjZiYchwB",
  "cloakingBaseUrl": "https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB"
}
```

## Troubleshooting

### Issue: 404 errors on all pages

**Solution:**
- Ensure `.htaccess` is in the `public/` directory
- Enable `mod_rewrite` in Apache: `a2enmod rewrite`
- Restart Apache: `sudo systemctl restart apache2`

### Issue: Admin login not working

**Solution:**
- Clear browser cookies
- Check that `storage/` directory is writable: `chmod 755 storage`
- Verify PHP sessions are enabled

### Issue: CSV not uploading

**Solution:**
- Check `storage/csv/` directory permissions: `chmod 755 storage/csv`
- Verify CSV file format
- Check file size (should be < 10MB)

### Issue: Images not displaying

**Solution:**
- Verify image URLs in CSV are correct
- Check CORS settings if images are from external domains
- Ensure placeholder image exists: `/assets/images/placeholder.svg`

## API Testing

### Test Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"sofaraway"}'
```

### Test Config

```bash
curl http://localhost:8000/api/config
```

### Test Products

```bash
curl "http://localhost:8000/api/products?page=1&limit=20"
```

### Test Search

```bash
curl "http://localhost:8000/api/products?action=search&keyword=test"
```

## Performance Optimization

### 1. Enable Caching

Add to `.htaccess`:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
</IfModule>
```

### 2. Enable Gzip Compression

Add to `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### 3. Optimize CSV Files

- Keep CSV files under 10MB
- Split large CSV files by category
- Use efficient CSV parsing

## Deployment

### Apache

1. Copy project to web root: `/var/www/html/delasof2026_php`
2. Set permissions:
   ```bash
   chmod 755 /var/www/html/delasof2026_php
   chmod 755 /var/www/html/delasof2026_php/storage
   chmod 755 /var/www/html/delasof2026_php/storage/csv
   ```
3. Enable mod_rewrite: `a2enmod rewrite`
4. Create virtual host configuration
5. Restart Apache: `systemctl restart apache2`

### Nginx

1. Copy project to web root: `/var/www/html/delasof2026_php`
2. Create Nginx configuration:
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       root /var/www/html/delasof2026_php/public;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php-fpm.sock;
           fastcgi_index index.php;
           include fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       }
   }
   ```
3. Test and reload: `nginx -t && systemctl reload nginx`

## Security Checklist

- [ ] Change default admin password
- [ ] Use HTTPS for all connections
- [ ] Validate CSV file uploads
- [ ] Implement rate limiting
- [ ] Add CSRF protection
- [ ] Sanitize all user inputs
- [ ] Use prepared statements for database queries
- [ ] Implement proper error handling
- [ ] Set appropriate file permissions
- [ ] Keep PHP and dependencies updated

## Monitoring

### Check Logs

```bash
tail -f storage/logs/error.log
```

### Monitor Performance

- Check PHP-FPM status
- Monitor server resources
- Track API response times
- Monitor CSV file sizes

## Backup

### Regular Backups

```bash
# Backup configuration
cp storage/config.json storage/config.json.backup

# Backup CSV files
tar -czf storage/csv_backup.tar.gz storage/csv/

# Full backup
tar -czf delasof2026_backup.tar.gz delasof2026_php/
```

## Updates

To update the application:

1. Backup current installation
2. Download latest version
3. Copy new files (except storage/)
4. Test thoroughly
5. Deploy to production

## Support

For issues or questions, contact: info@delasof.com
