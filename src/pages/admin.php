<?php
/**
 * Admin Panel Page
 */

$config = Config::get();

// Check if user is logged in
$isLoggedIn = Auth::isLoggedIn();

// Get success/error messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Get CSV files
$csvFiles = [];
if (is_dir(CSV_PATH)) {
    $files = scandir(CSV_PATH);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
            $csvFiles[] = pathinfo($file, PATHINFO_FILENAME);
        }
    }
}

ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <?php if ($success): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!$isLoggedIn): ?>
        <!-- Login Form -->
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6 text-center">Admin Login</h1>
                
                <form method="POST" action="/" class="space-y-4">
                    <input type="hidden" name="action" value="login">
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">Username</label>
                        <input type="text" name="username" required
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                        Login
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- Admin Panel -->
        <h1 class="text-4xl font-bold mb-8">Admin Panel</h1>
        
        <div class="mb-4">
            <a href="/?action=logout" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition">
                Logout
            </a>
        </div>
        
        <!-- Tabs -->
        <div class="mb-8 border-b border-border">
            <div class="flex gap-4">
                <button onclick="switchTab('settings')" id="tab-settings" class="px-4 py-2 border-b-2 border-blue-600 font-bold text-blue-600">
                    Settings
                </button>
                <button onclick="switchTab('csv')" id="tab-csv" class="px-4 py-2 border-b-2 border-transparent font-bold text-gray-600 hover:text-blue-600">
                    CSV Management
                </button>
            </div>
        </div>
        
        <!-- Settings Tab -->
        <div id="tab-content-settings" class="tab-content">
            <div class="max-w-2xl">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Site Settings</h2>
                    
                    <form method="POST" action="/" class="space-y-6">
                        <input type="hidden" name="action" value="save_config">
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Site Name</label>
                            <input type="text" name="site_name" value="<?php echo htmlspecialchars($config['siteName'] ?? 'ThaiDeals'); ?>"
                                   class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">URL Cloaking Token</label>
                            <input type="text" name="cloaking_token" value="<?php echo htmlspecialchars($config['cloakingToken'] ?? 'QlpXZyCqMylKUjZiYchwB'); ?>"
                                   class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">URL Cloaking Base URL</label>
                            <input type="text" name="cloaking_base_url" value="<?php echo htmlspecialchars($config['cloakingBaseUrl'] ?? 'https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB'); ?>"
                                   class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="enable_flash_sale" <?php echo ($config['enableFlashSale'] ?? false) ? 'checked' : ''; ?>>
                                <span>Enable Flash Sale</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="enable_ai_reviews" <?php echo ($config['enableAiReviews'] ?? false) ? 'checked' : ''; ?>>
                                <span>Enable AI Reviews</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                            Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- CSV Management Tab -->
        <div id="tab-content-csv" class="tab-content hidden">
            <div class="max-w-2xl">
                <!-- Upload CSV -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-6">Upload CSV File</h2>
                    
                    <form method="POST" action="/" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="action" value="upload_csv">
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Category Name</label>
                            <input type="text" name="category" value="สินค้าแนะนำ" required
                                   class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., สินค้าแนะนำ">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Select CSV File</label>
                            <input type="file" name="csv_file" accept=".csv" required
                                   class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                            Upload CSV
                        </button>
                    </form>
                </div>
                
                <!-- CSV Files List -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">CSV Files</h2>
                    
                    <?php if (empty($csvFiles)): ?>
                        <p class="text-gray-600">No CSV files uploaded yet</p>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($csvFiles as $file): ?>
                                <div class="flex items-center justify-between p-4 border border-border rounded-lg">
                                    <span class="font-medium"><?php echo htmlspecialchars($file); ?></span>
                                    <form method="POST" action="/" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_csv">
                                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($file); ?>">
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition"
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function switchTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Remove active state from all buttons
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        el.classList.remove('border-blue-600', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-600');
    });
    
    // Show selected tab
    document.getElementById('tab-content-' + tab).classList.remove('hidden');
    
    // Add active state to button
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-600');
    document.getElementById('tab-' + tab).classList.add('border-blue-600', 'text-blue-600');
}
</script>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, 'Admin Panel');
?>
