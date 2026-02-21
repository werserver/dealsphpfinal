<?php
/**
 * 404 Not Found Page
 */

$config = Config::get();

ob_start();
?>

<div class="container mx-auto px-4 py-12 text-center">
    <h1 class="text-6xl font-bold mb-4">404</h1>
    <p class="text-2xl text-gray-600 mb-8">ไม่พบหน้าที่คุณกำลังมองหา</p>
    <a href="/" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition inline-block">
        กลับไปหน้าแรก
    </a>
</div>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, 'ไม่พบหน้า');
?>
