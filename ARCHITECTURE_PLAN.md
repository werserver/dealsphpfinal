# Delasof2026 PHP Conversion - Architecture Plan

## Project Overview
Convert the React/Node.js Delasof2026 affiliate shop to a pure PHP application with identical UI/UX, features, and functionality.

## Key Features to Implement

### 1. Frontend (HTML/CSS/JavaScript)
- **Homepage (Index)**: Product listing with categories, search, filtering, pagination
- **Product Detail Page**: Individual product information with URL cloaking
- **Category Page**: Products filtered by category
- **Wishlist Page**: Saved products (localStorage)
- **Admin Panel**: Configuration management, CSV upload, settings
- **Admin Login**: Session-based authentication (admin/sofaraway)
- **About & Contact Pages**: Static pages
- **Sitemap Page**: XML sitemap generation
- **Header & Footer**: Navigation and branding
- **Theme Toggle**: Light/dark mode support

### 2. Backend (PHP)
- **Session Management**: Admin authentication (session-based)
- **Configuration Storage**: Server-side config persistence (JSON file)
- **CSV Management**: Upload, store, retrieve product data
- **API Endpoints**:
  - `GET /api/config` - Load global configuration
  - `POST /api/config` - Save configuration (admin only)
  - `GET /api/products` - Fetch products with pagination/filtering
  - `GET /api/products/search` - Search products
  - `GET /api/categories` - Get all categories
  - `POST /api/login` - Admin login
  - `GET /api/logout` - Admin logout
  - `POST /api/csv/upload` - Upload CSV (admin only)
  - `GET /api/csv/download/:category` - Download CSV
  - `DELETE /api/csv/:category` - Delete CSV (admin only)
  - `GET /sitemap.xml` - Generate XML sitemap

### 3. Data Storage
- **Config Storage**: `/storage/config.json` - Site settings, categories, keywords, theme
- **CSV Storage**: `/storage/csv/` - Category-based CSV files
- **Session Storage**: PHP sessions for admin authentication

### 4. URL Cloaking Configuration
- **Base URL**: `https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB`
- **Token**: `QlpXZyCqMylKUjZiYchwB`
- **Format**: `{base_url}&url={encoded_product_url}&source=api_product`
- **Example**: `https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB&url=https%3A%2F%2Fshopee.co.th%2Fproduct&source=api_product`

### 5. Admin Credentials
- **Username**: `admin`
- **Password**: `sofaraway`

## File Structure

```
/home/ubuntu/delasof2026_php/
├── public/
│   ├── index.php                 # Main entry point
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css         # Global styles
│   │   │   └── tailwind.css      # Tailwind CSS
│   │   ├── js/
│   │   │   ├── app.js            # Main app logic
│   │   │   ├── theme.js          # Theme toggle
│   │   │   └── wishlist.js       # Wishlist management
│   │   └── images/               # Static images
│   ├── sitemap.xml               # XML sitemap (generated)
│   └── robots.txt
├── src/
│   ├── api/
│   │   ├── auth.php              # Login/logout endpoints
│   │   ├── config.php            # Config management
│   │   ├── products.php          # Product data endpoints
│   │   ├── csv.php               # CSV management
│   │   └── sitemap.php           # Sitemap generation
│   ├── pages/
│   │   ├── home.php              # Homepage
│   │   ├── product-detail.php    # Product detail
│   │   ├── category.php          # Category page
│   │   ├── wishlist.php          # Wishlist page
│   │   ├── admin.php             # Admin panel
│   │   ├── about.php             # About page
│   │   ├── contact.php           # Contact page
│   │   └── 404.php               # Not found
│   ├── components/
│   │   ├── header.php            # Header component
│   │   ├── footer.php            # Footer component
│   │   ├── product-card.php      # Product card
│   │   ├── product-grid.php      # Product grid
│   │   ├── pagination.php        # Pagination
│   │   ├── filter-bar.php        # Filter bar
│   │   └── search-bar.php        # Search bar
│   ├── lib/
│   │   ├── config.php            # Config helper
│   │   ├── auth.php              # Authentication
│   │   ├── csv-parser.php        # CSV parsing
│   │   ├── url-builder.php       # URL cloaking
│   │   ├── db.php                # Database/storage
│   │   └── utils.php             # Utility functions
│   └── middleware/
│       ├── auth-middleware.php   # Admin auth check
│       └── session-middleware.php # Session handling
├── storage/
│   ├── config.json               # Configuration file
│   ├── csv/                      # CSV files storage
│   └── logs/                     # Log files
├── .htaccess                     # URL rewriting rules
├── composer.json                 # PHP dependencies
└── README.md                     # Documentation
```

## Database Schema (CSV Columns)

Based on the products.csv structure:
- `product_id`: Unique identifier
- `product_url`: Original product URL
- `product_name`: Product title
- `product_price`: Original price
- `product_discounted`: Discounted price
- `product_min_price`: Minimum price
- `product_max_price`: Maximum price
- `product_discount_percentage`: Discount percentage
- `product_sold`: Number sold
- `product_rating`: Star rating
- `product_review_count`: Review count
- `product_shop_id`: Shop ID
- `product_shop_name`: Shop name
- `product_shop_location`: Shop location
- `product_image`: Main product image
- `product_images`: Additional images (JSON array)
- `product_description`: Product description
- `category_name`: Product category

## Implementation Steps

1. **Phase 1**: Set up PHP project structure and routing
2. **Phase 2**: Create backend API endpoints
3. **Phase 3**: Build frontend pages and components
4. **Phase 4**: Implement admin panel
5. **Phase 5**: Add CSV management and data persistence
6. **Phase 6**: Implement URL cloaking and product linking
7. **Phase 7**: Add theme toggle and styling
8. **Phase 8**: Testing and optimization

## Technology Stack

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript (Vanilla)
- **Storage**: File-based JSON and CSV
- **Session**: PHP sessions
- **Routing**: URL rewriting via .htaccess

## Configuration Example

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
  "selectedAdvertisers": [],
  "enableFlashSale": true,
  "enableAiReviews": false,
  "defaultCurrency": "THB",
  "themeColor": "light",
  "cloakingBaseUrl": "https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB",
  "cloakingToken": "QlpXZyCqMylKUjZiYchwB"
}
```

## Notes

- All configuration and product categories will be stored server-side (JSON/CSV files)
- Admin session will be maintained using PHP sessions
- URL cloaking will be applied to all product links
- Frontend will be responsive and match the original React design
- Wishlist will use browser localStorage for client-side persistence
