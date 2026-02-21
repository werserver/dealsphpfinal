# Delasof2026 PHP - Affiliate Shop Platform

ระบบจัดการร้านค้าออนไลน์แบบ affiliate ที่สมบูรณ์พร้อมใช้งาน สำหรับเซิร์ฟเวอร์ PHP ธรรมชาติ

## 🎯 ลักษณะเด่น

- ✅ **Standalone PHP** - ไม่ต้องติดตั้ง API backend
- ✅ **URL Cloaking** - ซ่อน URL สินค้าจริง
- ✅ **CSV Management** - อัพโหลดและจัดการสินค้าผ่าน CSV
- ✅ **Admin Panel** - ระบบจัดการแบบสมบูรณ์
- ✅ **Responsive Design** - ใช้งานได้บนทุกอุปกรณ์
- ✅ **No Dependencies** - ไม่ต้องติดตั้งอะไรพิเศษ
- ✅ **Ready for Production** - พร้อมใช้งานบนโฮสติ้งปกติ

## 📋 ความต้องการ

- PHP 7.4 หรือสูงกว่า
- เซิร์ฟเวอร์ที่รองรับ `.htaccess` (Apache)
- Write permission สำหรับ `storage/` directory

## 🚀 การติดตั้ง

### 1. อัพโหลดไฟล์

```bash
# Clone repository
git clone https://github.com/werserver/dealsphpfinal.git
cd dealsphpfinal

# หรือ extract จาก ZIP file
unzip dealsphpfinal.zip
cd dealsphpfinal
```

### 2. ตั้งค่า Permissions

```bash
# ให้สิทธิ์เขียนสำหรับ storage directory
chmod 755 storage
chmod 755 storage/csv
chmod 755 storage/logs
```

### 3. เรียกใช้งาน

#### ตัวเลือก A: ใช้ PHP Built-in Server (สำหรับทดสอบ)

```bash
cd /path/to/dealsphpfinal
php -S localhost:8000
```

จากนั้นเข้าไปที่ `http://localhost:8000`

#### ตัวเลือก B: ใช้ Apache

```bash
# ตั้งค่า Virtual Host
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /path/to/dealsphpfinal
    
    <Directory /path/to/dealsphpfinal>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 🔐 Admin Login

**URL:** `http://yoursite.com/admin`

**Default Credentials:**
- Username: `admin`
- Password: `sofaraway`

## 📁 โครงสร้างโปรเจกต์

```
delasof2026_php/
├── index.php                 # Entry point
├── .htaccess                 # URL rewriting
├── assets/                   # Static files
│   ├── css/
│   ├── js/
│   └── images/
├── src/
│   ├── lib/                  # Core libraries
│   │   ├── config.php
│   │   ├── auth.php
│   │   ├── url-builder.php
│   │   ├── csv-parser.php
│   │   └── utils.php
│   ├── pages/                # Page templates
│   │   ├── home.php
│   │   ├── admin.php
│   │   ├── product.php
│   │   ├── category.php
│   │   ├── wishlist.php
│   │   ├── about.php
│   │   ├── contact.php
│   │   └── 404.php
│   └── components/           # Reusable components
│       └── layout.php
├── storage/                  # Data storage
│   ├── config.json          # Configuration
│   ├── csv/                 # CSV files
│   └── logs/                # Error logs
└── README.md
```

## ⚙️ Configuration

### config.json

ไฟล์ `storage/config.json` ใช้สำหรับเก็บการตั้งค่า:

```json
{
  "siteName": "ThaiDeals",
  "cloakingToken": "QlpXZyCqMylKUjZiYchwB",
  "cloakingBaseUrl": "https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB",
  "categories": ["สินค้าแนะนำ", "อิเล็กทรอนิกส์"],
  "enableFlashSale": false,
  "enableAiReviews": false
}
```

### URL Cloaking

ระบบจะสร้างลิงก์แบบ cloaked สำหรับสินค้าทั้งหมด:

```
Format: {baseUrl}&url={encoded_url}&source=api_product

ตัวอย่าง:
https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB&url=https%3A%2F%2Fshopee.co.th%2Fproduct&source=api_product
```

## 📊 CSV Format

ไฟล์ CSV ต้องมีคอลัมน์ต่อไปนี้:

```csv
product_name,product_price,product_discounted,product_discount_percentage,product_rating,product_review_count,product_shop_name,product_url,product_image
iPhone 13,25000,22000,12,4.8,1250,Apple Store,https://shopee.co.th/...,https://example.com/image.jpg
```

**คอลัมน์ที่จำเป็น:**
- `product_name` - ชื่อสินค้า
- `product_price` - ราคาเต็ม
- `product_discounted` - ราคาลด (0 ถ้าไม่มี)
- `product_discount_percentage` - เปอร์เซ็นต์ส่วนลด
- `product_rating` - คะแนน (0-5)
- `product_review_count` - จำนวนรีวิว
- `product_shop_name` - ชื่อร้าน
- `product_url` - URL สินค้า
- `product_image` - URL รูปภาพ

## 🎨 Features

### Homepage
- ค้นหาสินค้า
- ตัวกรองและเรียงลำดับ
- แสดงสินค้าแบบ Grid
- Pagination

### Admin Panel
- **Settings Tab**
  - ตั้งค่าชื่อไซต์
  - ตั้งค่า URL Cloaking
  - เปิด/ปิด Flash Sale
  - เปิด/ปิด AI Reviews

- **CSV Management Tab**
  - อัพโหลด CSV ใหม่
  - ลบ CSV เก่า
  - แสดงรายการ CSV ที่อัพโหลด

### Product Pages
- หน้ารายละเอียดสินค้า
- หน้าหมวดหมู่
- หน้า Wishlist
- หน้า About & Contact

## 🔧 Troubleshooting

### ปัญหา: 404 Not Found

**วิธีแก้:** ตรวจสอบว่า `.htaccess` อยู่ใน root directory และ Apache มี `mod_rewrite` เปิดใช้งาน

```bash
# ตรวจสอบ mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### ปัญหา: Permission Denied

**วิธีแก้:** ให้สิทธิ์เขียนสำหรับ storage directory

```bash
chmod -R 755 storage
chown -R www-data:www-data storage  # สำหรับ Apache
```

### ปัญหา: CSV ไม่อัพโหลดได้

**วิธีแก้:**
1. ตรวจสอบขนาดไฟล์ (สูงสุด 10MB)
2. ตรวจสอบ format (ต้องเป็น CSV)
3. ตรวจสอบ permissions สำหรับ `storage/csv/`

## 🚀 Deployment

### ขั้นตอนการ Deploy

1. **Upload ไฟล์ไปยังเซิร์ฟเวอร์**
   ```bash
   scp -r delasof2026_php user@server:/var/www/
   ```

2. **ตั้งค่า Permissions**
   ```bash
   ssh user@server
   cd /var/www/delasof2026_php
   chmod 755 storage storage/csv storage/logs
   ```

3. **ตั้งค่า Virtual Host** (ถ้าใช้ Apache)
   ```apache
   <VirtualHost *:80>
       ServerName yourdomain.com
       DocumentRoot /var/www/delasof2026_php
       
       <Directory /var/www/delasof2026_php>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

4. **Enable Rewrite Module**
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

## 📞 Support

สำหรับปัญหาหรือคำถาม กรุณาติดต่อ:
- Email: support@example.com
- GitHub Issues: https://github.com/werserver/dealsphpfinal/issues

## 📄 License

MIT License - ใช้งานได้อย่างอิสระ

## 🎉 Version

**Version:** 2.0.0 (Standalone PHP)  
**Last Updated:** February 2026

---

Made with ❤️ for Thai E-commerce
