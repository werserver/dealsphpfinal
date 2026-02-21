<?php
/**
 * Main Layout Template
 */

function renderLayout($content, $title = 'ThaiDeals', $description = 'ช้อปปิ้งออนไลน์ที่ดีที่สุด') {
    $config = Config::get();
    $siteName = $config['siteName'] ?? 'ThaiDeals';
    $isAdmin = Auth::isLoggedIn();
    $baseUrl = getBaseUrl();
    ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="theme-color" content="#3b82f6">
    <title><?php echo htmlspecialchars($title); ?> - <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-background text-foreground font-sans">
    <div id="app">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white border-b border-border shadow-sm">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between gap-4">
                    <a href="/" class="text-2xl font-bold text-primary">
                        <?php echo htmlspecialchars($siteName); ?>
                    </a>
                    <nav class="hidden md:flex items-center gap-6">
                        <a href="/" class="text-sm font-medium hover:text-primary transition">หน้าแรก</a>
                        <a href="/wishlist" class="text-sm font-medium hover:text-primary transition">รายการโปรด</a>
                        <a href="/about" class="text-sm font-medium hover:text-primary transition">เกี่ยวกับ</a>
                        <a href="/contact" class="text-sm font-medium hover:text-primary transition">ติดต่อ</a>
                        <?php if ($isAdmin): ?>
                            <a href="/admin" class="text-sm font-medium text-orange-600 hover:text-orange-700 transition">⚙️ Admin</a>
                            <a href="/api/auth/logout" class="text-sm font-medium text-red-600 hover:text-red-700 transition" onclick="handleLogout(event)">ออกจากระบบ</a>
                        <?php else: ?>
                            <a href="/admin" class="text-sm font-medium hover:text-primary transition">Admin</a>
                        <?php endif; ?>
                    </nav>
                    <button class="md:hidden p-2 hover:bg-gray-100 rounded-lg" id="mobileMenuBtn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="min-h-screen">
            <?php echo $content; ?>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-12">
            <div class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h3 class="font-bold text-lg mb-4"><?php echo htmlspecialchars($siteName); ?></h3>
                        <p class="text-gray-400 text-sm">ช้อปปิ้งออนไลน์ที่ดีที่สุดกับราคาที่ถูกที่สุด</p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">ลิงก์</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li><a href="/" class="hover:text-white transition">หน้าแรก</a></li>
                            <li><a href="/about" class="hover:text-white transition">เกี่ยวกับ</a></li>
                            <li><a href="/contact" class="hover:text-white transition">ติดต่อ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">ช่วยเหลือ</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li><a href="#" class="hover:text-white transition">คำถามที่พบบ่อย</a></li>
                            <li><a href="#" class="hover:text-white transition">นโยบายความเป็นส่วนตัว</a></li>
                            <li><a href="#" class="hover:text-white transition">เงื่อนไขการใช้บริการ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">ติดต่อเรา</h4>
                        <p class="text-gray-400 text-sm mb-2">Email: info@thaideals.com</p>
                        <p class="text-gray-400 text-sm">Phone: +66 2-XXX-XXXX</p>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-center text-gray-400 text-sm">
                    <p>&copy; 2026 <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="/assets/js/app.js"></script>
    <script>
        // Theme toggle
        document.addEventListener('DOMContentLoaded', function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        });

        // Mobile menu
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            // Mobile menu toggle logic
        });

        // Logout handler
        function handleLogout(e) {
            e.preventDefault();
            fetch('/api/auth/logout', { method: 'GET' })
                .then(() => window.location.href = '/')
                .catch(err => console.error('Logout error:', err));
        }
    </script>
</body>
</html>
    <?php
}
?>
