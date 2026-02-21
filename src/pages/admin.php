<?php
/**
 * Admin Panel
 */

if (!Auth::isLoggedIn()) {
    // Show login form
    ob_start();
    ?>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
            <h1 class="text-3xl font-bold mb-6 text-center">Admin Login</h1>
            
            <form id="loginForm" onsubmit="handleLogin(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    เข้าสู่ระบบ
                </button>
            </form>

            <div id="errorMsg" class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg hidden"></div>
        </div>
    </div>

    <script>
    async function handleLogin(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const errorMsg = document.getElementById('errorMsg');

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = '/admin';
            } else {
                errorMsg.textContent = data.error || 'Login failed';
                errorMsg.classList.remove('hidden');
            }
        } catch (error) {
            errorMsg.textContent = 'Error: ' + error.message;
            errorMsg.classList.remove('hidden');
        }
    }
    </script>
    <?php
    $content = ob_get_clean();
    require_once SRC_PATH . '/components/layout.php';
    renderLayout($content, 'Admin Login');
    exit;
}

// Admin is logged in - show admin panel
$config = Config::get();

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">🛠 Admin Panel</h1>
        <button onclick="handleLogout()" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition">
            ออกจากระบบ
        </button>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="flex border-b border-border">
            <button class="tab-btn active px-6 py-4 font-bold text-blue-600 border-b-2 border-blue-600" data-tab="settings">
                ⚙️ ตั้งค่า
            </button>
            <button class="tab-btn px-6 py-4 font-bold text-gray-600 hover:text-blue-600" data-tab="csv">
                📊 จัดการ CSV
            </button>
            <button class="tab-btn px-6 py-4 font-bold text-gray-600 hover:text-blue-600" data-tab="categories">
                🏷️ หมวดหมู่
            </button>
        </div>

        <!-- Settings Tab -->
        <div id="settings" class="tab-content p-6">
            <h2 class="text-2xl font-bold mb-6">ตั้งค่าเว็บไซต์</h2>
            
            <form id="settingsForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2">ชื่อเว็บไซต์</label>
                    <input type="text" id="siteName" value="<?php echo htmlspecialchars($config['siteName']); ?>"
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Token URL Cloaking</label>
                    <input type="text" id="cloakingToken" value="<?php echo htmlspecialchars($config['cloakingToken'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Base URL Cloaking</label>
                    <input type="text" id="cloakingBaseUrl" value="<?php echo htmlspecialchars($config['cloakingBaseUrl'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="enableFlashSale" <?php echo $config['enableFlashSale'] ? 'checked' : ''; ?>
                               class="w-4 h-4">
                        <span class="font-medium">เปิดใช้ Flash Sale</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="enableAiReviews" <?php echo $config['enableAiReviews'] ? 'checked' : ''; ?>
                               class="w-4 h-4">
                        <span class="font-medium">เปิดใช้ AI Reviews</span>
                    </label>
                </div>

                <button type="button" onclick="saveSettings()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    💾 บันทึก
                </button>
            </form>
        </div>

        <!-- CSV Management Tab -->
        <div id="csv" class="tab-content p-6 hidden">
            <h2 class="text-2xl font-bold mb-6">จัดการไฟล์ CSV</h2>
            
            <div class="mb-8">
                <h3 class="text-lg font-bold mb-4">อัปโหลด CSV ใหม่</h3>
                <div class="border-2 border-dashed border-blue-300 rounded-lg p-8 text-center">
                    <input type="file" id="csvFile" accept=".csv" class="hidden">
                    <button type="button" onclick="document.getElementById('csvFile').click()" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                        เลือกไฟล์ CSV
                    </button>
                    <p class="text-gray-600 mt-2">หรือลากไฟล์มาที่นี่</p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4">ไฟล์ CSV ที่มีอยู่</h3>
                <div id="csvList" class="space-y-2">
                    <?php 
                    $csvFiles = CsvParser::getCsvFiles();
                    if (empty($csvFiles)): 
                    ?>
                        <p class="text-gray-600">ยังไม่มีไฟล์ CSV</p>
                    <?php else: ?>
                        <?php foreach ($csvFiles as $file): ?>
                            <div class="flex justify-between items-center p-4 bg-gray-100 rounded-lg">
                                <span class="font-medium"><?php echo htmlspecialchars($file); ?></span>
                                <button type="button" onclick="deleteCsv('<?php echo htmlspecialchars($file); ?>')" 
                                        class="bg-red-600 text-white px-4 py-1 rounded text-sm hover:bg-red-700 transition">
                                    ลบ
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div id="categories" class="tab-content p-6 hidden">
            <h2 class="text-2xl font-bold mb-6">จัดการหมวดหมู่</h2>
            
            <div class="mb-8">
                <h3 class="text-lg font-bold mb-4">เพิ่มหมวดหมู่ใหม่</h3>
                <div class="flex gap-2">
                    <input type="text" id="newCategory" placeholder="ชื่อหมวดหมู่"
                           class="flex-1 px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="addCategory()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                        ➕ เพิ่ม
                    </button>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4">หมวดหมู่ที่มีอยู่</h3>
                <div id="categoriesList" class="space-y-2">
                    <?php foreach ($config['categories'] as $category): ?>
                        <div class="flex justify-between items-center p-4 bg-gray-100 rounded-lg">
                            <span class="font-medium"><?php echo htmlspecialchars($category); ?></span>
                            <button type="button" onclick="deleteCategory('<?php echo htmlspecialchars($category); ?>')" 
                                    class="bg-red-600 text-white px-4 py-1 rounded text-sm hover:bg-red-700 transition">
                                ลบ
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
            b.classList.add('text-gray-600', 'hover:text-blue-600');
        });
        
        // Show selected tab
        document.getElementById(tabName).classList.remove('hidden');
        
        // Add active class to clicked button
        this.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
        this.classList.remove('text-gray-600', 'hover:text-blue-600');
    });
});

async function saveSettings() {
    const config = {
        siteName: document.getElementById('siteName').value,
        cloakingToken: document.getElementById('cloakingToken').value,
        cloakingBaseUrl: document.getElementById('cloakingBaseUrl').value,
        enableFlashSale: document.getElementById('enableFlashSale').checked,
        enableAiReviews: document.getElementById('enableAiReviews').checked
    };

    try {
        const response = await fetch('/api/config', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(config)
        });

        const data = await response.json();
        if (data.success) {
            alert('บันทึกสำเร็จ!');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

function handleLogout() {
    if (confirm('ต้องการออกจากระบบหรือไม่?')) {
        fetch('/api/auth/logout', { method: 'GET' })
            .then(() => window.location.href = '/')
            .catch(err => console.error('Logout error:', err));
    }
}

function deleteCsv(category) {
    if (confirm('ต้องการลบไฟล์ CSV นี้หรือไม่?')) {
        fetch('/api/csv?category=' + encodeURIComponent(category), { method: 'DELETE' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
    }
}

function deleteCategory(category) {
    if (confirm('ต้องการลบหมวดหมู่นี้หรือไม่?')) {
        // Implementation for deleting category
        alert('Feature coming soon');
    }
}

function addCategory() {
    const categoryName = document.getElementById('newCategory').value;
    if (!categoryName) {
        alert('กรุณากรอกชื่อหมวดหมู่');
        return;
    }
    // Implementation for adding category
    alert('Feature coming soon');
}
</script>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, 'Admin Panel');
?>
