# Delasof2026 PHP Version

A complete PHP port of the Delasof2026 affiliate shop platform with product listings, admin panel, URL cloaking, and wishlist functionality.

## Features

✅ **Product Management**
- Display products from CSV files
- Search and filter products
- Category-based browsing
- Pagination support
- Product detail pages

✅ **Admin Panel**
- Admin login (username: `admin`, password: `sofaraway`)
- Configuration management
- CSV file upload and management
- Category management
- Theme color settings
- URL cloaking configuration

✅ **URL Cloaking**
- Default Base URL: `https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB`
- Default Token: `QlpXZyCqMylKUjZiYchwB`
- Automatic URL encoding and parameter appending

✅ **User Features**
- Product search and filtering
- Wishlist (localStorage-based)
- Responsive design
- Light/Dark theme support
- SEO-friendly URLs

✅ **API Endpoints**
- `/api/auth/login` - Admin login
- `/api/auth/logout` - Admin logout
- `/api/config` - Get/update configuration
- `/api/products` - List/search products
- `/api/csv` - Manage CSV files
- `/sitemap.xml` - XML sitemap

## Installation

### Requirements
- PHP 7.4 or higher
- Web server (Apache with mod_rewrite or Nginx)
- Modern web browser

### Setup Steps

1. **Clone or extract the project**
   ```bash
   cd delasof2026_php
   ```

2. **Create storage directories**
   ```bash
   mkdir -p storage/csv storage/logs
   chmod 755 storage storage/csv storage/logs
   ```

3. **Configure web server**

   **Apache (.htaccess already included):**
   - Ensure `mod_rewrite` is enabled
   - The `.htaccess` file in `public/` handles URL rewriting

   **Nginx:**
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```

4. **Set file permissions**
   ```bash
   chmod 755 public
   chmod 644 public/index.php
   chmod 755 storage
   ```

5. **Access the application**
   - Frontend: `http://localhost/delasof2026_php/public/`
   - Admin: `http://localhost/delasof2026_php/public/admin`

## Admin Login

- **Username:** `admin`
- **Password:** `sofaraway`

## Configuration

### Default Settings

Configuration is stored in `storage/config.json`:

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
  "keywords": [...],
  "enableFlashSale": true,
  "enableAiReviews": false,
  "defaultCurrency": "THB",
  "cloakingToken": "QlpXZyCqMylKUjZiYchwB",
  "cloakingBaseUrl": "https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB"
}
```

### URL Cloaking Configuration

The application uses URL cloaking to redirect product links through a tracking service:

**Base URL Format:**
```
https://goeco.mobi/?token=YOUR_TOKEN&url=ENCODED_PRODUCT_URL&source=api_product
```

**Example:**
```
https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB&url=https%3A%2F%2Fshopee.co.th%2Fproduct&source=api_product
```

You can customize the token and base URL in the Admin Panel.

## CSV File Format

CSV files should be placed in `storage/csv/` directory with the following columns:

```
product_id,product_url,product_name,product_price,product_discounted,product_min_price,product_max_price,product_sold,product_discount_percentage,product_discount_text,product_shop_id,product_shop_name,product_shop_location,product_rating,product_review_count,...
```

### Example CSV Row:
```
24462649604,"https://shopee.co.th/product","สระน้ำเป่าลม","798.00","160.00","160.00","360.00",56,"72%","ซื้อ 2 ชิ้น ลด ฿10",1071816656,"TrailQuest","สมุทรปราการ",4.8,2812,...
```

## Directory Structure

```
delasof2026_php/
├── public/
│   ├── index.php              # Main entry point
│   ├── .htaccess              # URL rewriting rules
│   └── assets/
│       ├── css/
│       │   ├── style.css      # Custom styles
│       │   └── tailwind.css   # Tailwind CSS
│       ├── js/
│       │   └── app.js         # Main JavaScript
│       └── images/
│           └── placeholder.svg
├── src/
│   ├── api/
│   │   ├── auth.php           # Authentication endpoints
│   │   ├── config.php         # Configuration endpoints
│   │   ├── products.php       # Product endpoints
│   │   ├── csv.php            # CSV management
│   │   └── sitemap.php        # Sitemap generation
│   ├── pages/
│   │   ├── home.php           # Homepage
│   │   ├── admin.php          # Admin panel
│   │   ├── about.php          # About page
│   │   ├── contact.php        # Contact page
│   │   ├── wishlist.php       # Wishlist page
│   │   └── 404.php            # 404 page
│   ├── components/
│   │   └── layout.php         # Main layout template
│   ├── lib/
│   │   ├── config.php         # Configuration management
│   │   ├── auth.php           # Authentication
│   │   ├── url-builder.php    # URL cloaking
│   │   ├── csv-parser.php     # CSV parsing
│   │   └── utils.php          # Utility functions
│   └── middleware/
├── storage/
│   ├── config.json            # Site configuration
│   ├── csv/                   # CSV files
│   └── logs/                  # Log files
└── README.md
```

## API Documentation

### Authentication

**POST /api/auth/login**
```json
{
  "username": "admin",
  "password": "sofaraway"
}
```

**GET /api/auth/logout**

### Configuration

**GET /api/config**
Returns the current site configuration.

**POST /api/config** (Admin only)
```json
{
  "siteName": "New Name",
  "cloakingToken": "new_token",
  ...
}
```

### Products

**GET /api/products?page=1&limit=20&sort=default**
List products with pagination.

**GET /api/products?action=search&keyword=search_term**
Search products.

**GET /api/products?action=category&category=category_name**
Get products by category.

### CSV Management

**GET /api/csv?category=category_name**
Get CSV content for a category.

**POST /api/csv?category=category_name** (Admin only)
```json
{
  "csvContent": "CSV content here..."
}
```

**DELETE /api/csv?category=category_name** (Admin only)
Delete CSV for a category.

## Features Overview

### Homepage
- Product grid with images, prices, ratings
- Search bar for product search
- Category navigation
- Sorting options (price, discount, rating)
- Pagination

### Admin Panel
- **Settings Tab:** Configure site name, URL cloaking, features
- **CSV Tab:** Upload and manage CSV files
- **Categories Tab:** Manage product categories

### Wishlist
- Add/remove products to wishlist
- Persistent storage using localStorage
- Quick access from any page

### URL Cloaking
- Automatic URL encoding
- Configurable base URL and token
- Tracking parameters appended to all product links

## Customization

### Changing Site Name
1. Go to Admin Panel
2. Update "Site Name" in Settings
3. Click Save

### Adding New Categories
1. Go to Admin Panel → Categories Tab
2. Enter category name and click Add
3. Upload CSV file for the category

### Modifying URL Cloaking
1. Go to Admin Panel → Settings
2. Update Token and Base URL
3. Click Save

## Troubleshooting

### 404 Errors
- Ensure `.htaccess` is in the `public/` directory
- Check that `mod_rewrite` is enabled (Apache)
- Verify the base URL in your web server configuration

### CSV Files Not Loading
- Check that `storage/csv/` directory exists and is writable
- Verify CSV file format matches the expected columns
- Check file permissions (should be 644)

### Admin Login Not Working
- Clear browser cookies and session storage
- Check that sessions are enabled in PHP
- Verify `storage/` directory is writable

### Images Not Displaying
- Ensure image URLs in CSV are correct and accessible
- Check CORS settings if images are from external domains
- Verify placeholder image exists at `/assets/images/placeholder.svg`

## Performance Tips

1. **Optimize CSV Files**
   - Keep CSV files reasonably sized (< 10MB)
   - Consider splitting large CSV files by category

2. **Enable Caching**
   - Use browser caching for static assets
   - Implement server-side caching for product data

3. **Database Alternative**
   - For large-scale deployments, consider migrating to a database

## Security Considerations

1. **Admin Credentials**
   - Change default admin password in production
   - Use HTTPS for all admin operations

2. **File Uploads**
   - Validate uploaded CSV files
   - Implement file size limits

3. **URL Cloaking**
   - Use HTTPS for cloaking URLs
   - Regularly update cloaking tokens

## Support

For issues or questions, please contact: info@delasof.com

## License

MIT License - See LICENSE file for details

## Changelog

### Version 1.0.0
- Initial PHP port of Delasof2026
- Complete feature parity with React version
- Admin panel with CSV management
- URL cloaking support
- Responsive design
