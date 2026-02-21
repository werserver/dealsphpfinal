# Delasof2026 PHP - XAMPP Setup Guide

## การติดตั้งบน XAMPP

### ขั้นตอนที่ 1: เตรียมไฟล์
1. ดาวน์โหลด/Clone โปรเจกต์ Delasof2026 PHP
2. Copy โฟลเดอร์ `delasof2026_php` ไปยัง `C:\xampp\htdocs\` (Windows) หรือ `/Applications/XAMPP/htdocs/` (Mac)

### ขั้นตอนที่ 2: ตั้งค่า Permissions (สำหรับ Linux/Mac)
```bash
cd /path/to/xampp/htdocs/delasof2026_php
chmod 755 storage
chmod 755 storage/csv
chmod 755 storage/logs
```

### ขั้นตอนที่ 3: เปิด XAMPP Control Panel
- Windows: เปิด XAMPP Control Panel
- Mac: เปิด XAMPP Manager
- Linux: ใช้ terminal

### ขั้นตอนที่ 4: เริ่ม Services
1. คลิก "Start" สำหรับ Apache
2. คลิก "Start" สำหรับ MySQL (ถ้าต้องการ)

### ขั้นตอนที่ 5: เข้าใช้งาน
เปิด browser และไปที่:
```
http://localhost/delasof2026_php/
```

## Admin Login
- **URL:** `http://localhost/delasof2026_php/admin`
- **Username:** `admin`
- **Password:** `sofaraway`

## URL Cloaking Configuration
ค่าเริ่มต้น:
- **Base URL:** `https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB`
- **Token:** `QlpXZyCqMylKUjZiYchwB`

สามารถเปลี่ยนแปลงได้ในแอดมิน panel

## CSV File Upload
1. ไปที่ Admin Panel (`/admin`)
2. ไปที่ CSV Management Tab
3. เลือกไฟล์ CSV และอัพโหลด
4. ระบบจะแสดงสินค้าจากไฟล์ CSV

## CSV Format
ไฟล์ CSV ต้องมีคอลัมน์ต่อไปนี้:
```
product_name,product_price,product_discounted,product_discount_percentage,product_rating,product_review_count,product_shop_name,product_url,product_image
```

## Features
- ✅ Product listing & search
- ✅ Category browsing
- ✅ Wishlist management
- ✅ Admin panel
- ✅ CSV management
- ✅ URL cloaking
- ✅ Responsive design

## Troubleshooting

### ปัญหา: 404 Not Found
**วิธีแก้:** ตรวจสอบว่า `.htaccess` อยู่ใน root directory

### ปัญหา: CSS/JS ไม่โหลด
**วิธีแก้:** ตรวจสอบ path ใน layout.php ให้ใช้ `getBaseUrl()` ถูกต้อง

### ปัญหา: CSV ไม่อัพโหลดได้
**วิธีแก้:** ตรวจสอบ permissions สำหรับ `storage/csv/` directory

### ปัญหา: Admin login ไม่ได้
**วิธีแก้:** ลบ cookies และ session storage ของ browser แล้วลองใหม่

## Support
สำหรับปัญหาเพิ่มเติม ติดต่อ: support@example.com

## Version
**Version:** 2.0.0 (XAMPP Compatible)
**Last Updated:** February 2026
