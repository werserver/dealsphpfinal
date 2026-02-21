<?php
/**
 * Contact Page
 */

$config = Config::get();

ob_start();
?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8">ติดต่อเรา</h1>
    
    <div class="max-w-3xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form onsubmit="handleContactForm(event)" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2">ชื่อ</label>
                    <input type="text" required
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">อีเมล</label>
                    <input type="email" required
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">เรื่อง</label>
                    <input type="text" required
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">ข้อความ</label>
                    <textarea required rows="6"
                              class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    ส่งข้อความ
                </button>
            </form>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h3 class="text-xl font-bold mb-4">📧 อีเมล</h3>
                <p class="text-gray-700">info@thaideals.com</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8">
                <h3 class="text-xl font-bold mb-4">📞 โทรศัพท์</h3>
                <p class="text-gray-700">+66 2-XXX-XXXX</p>
            </div>
        </div>
    </div>
</div>

<script>
function handleContactForm(e) {
    e.preventDefault();
    alert('ขอบคุณที่ติดต่อเรา! เราจะตอบกลับในเร็วๆ นี้');
    e.target.reset();
}
</script>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, 'ติดต่อเรา');
?>
