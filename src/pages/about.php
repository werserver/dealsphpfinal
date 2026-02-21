<?php
/**
 * About Page
 */

$config = Config::get();

ob_start();
?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8">เกี่ยวกับ <?php echo htmlspecialchars($config['siteName']); ?></h1>
    
    <div class="max-w-3xl">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4">ใครเรา?</h2>
            <p class="text-gray-700 mb-4">
                <?php echo htmlspecialchars($config['siteName']); ?> เป็นแพลตฟอร์มช้อปปิ้งออนไลน์ที่ช่วยให้คุณค้นหาสินค้าที่ดีที่สุดกับราคาที่ถูกที่สุด
            </p>
            <p class="text-gray-700">
                เรามุ่งมั่นที่จะให้บริการที่ดีที่สุดแก่ลูกค้าของเรา
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-4">ติดต่อเรา</h2>
            <p class="text-gray-700 mb-2">
                <strong>Email:</strong> info@thaideals.com
            </p>
            <p class="text-gray-700">
                <strong>Phone:</strong> +66 2-XXX-XXXX
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, 'เกี่ยวกับเรา');
?>
