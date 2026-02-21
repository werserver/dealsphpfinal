<?php
/**
 * Homepage - Product Listing
 */

$config = Config::get();
$categories = $config['categories'] ?? [];

// Get current page
$page = intval(getQueryParam('page', 1));
$keyword = sanitizeInput(getQueryParam('keyword', ''));
$sort = sanitizeInput(getQueryParam('sort', 'default'));

// Load products from all CSV files
$allProducts = [];
foreach ($categories as $category) {
    $products = CsvParser::loadCsvFromFile($category);
    foreach ($products as $product) {
        $product['category_name'] = $category;
        $allProducts[] = $product;
    }
}

// Filter by keyword if provided
if (!empty($keyword)) {
    $allProducts = array_filter($allProducts, function($p) use ($keyword) {
        return stripos($p['product_name'], $keyword) !== false || 
               stripos($p['product_shop_name'], $keyword) !== false;
    });
}

// Apply sorting
if ($sort === 'price-asc') {
    usort($allProducts, function($a, $b) {
        $priceA = $a['product_discounted'] ?: $a['product_price'];
        $priceB = $b['product_discounted'] ?: $b['product_price'];
        return $priceA <=> $priceB;
    });
} elseif ($sort === 'price-desc') {
    usort($allProducts, function($a, $b) {
        $priceA = $a['product_discounted'] ?: $a['product_price'];
        $priceB = $b['product_discounted'] ?: $b['product_price'];
        return $priceB <=> $priceA;
    });
} elseif ($sort === 'discount') {
    usort($allProducts, function($a, $b) {
        return $b['product_discount_percentage'] <=> $a['product_discount_percentage'];
    });
}

// Paginate
$limit = 20;
$total = count($allProducts);
$offset = ($page - 1) * $limit;
$products = array_slice($allProducts, $offset, $limit);
$totalPages = ceil($total / $limit);

// Apply URL cloaking
$token = $config['cloakingToken'] ?? UrlBuilder::getDefaultToken();
$baseUrl = $config['cloakingBaseUrl'] ?? UrlBuilder::getDefaultCloakingUrl();

foreach ($products as &$product) {
    if (!empty($product['product_url'])) {
        $product['cloaked_url'] = UrlBuilder::buildCloakedUrl($product['product_url'], $token, $baseUrl);
    }
}

// Generate content
ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <section class="mb-12">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-8 text-white">
            <h1 class="text-4xl font-bold mb-4">ยินดีต้อนรับสู่ <?php echo htmlspecialchars($config['siteName']); ?></h1>
            <p class="text-lg mb-6">ค้นหาสินค้าที่ดีที่สุดกับราคาที่ถูกที่สุด</p>
            
            <!-- Search Bar -->
            <form method="GET" action="/" class="flex gap-2">
                <input type="text" name="keyword" placeholder="ค้นหาสินค้า..." 
                       value="<?php echo htmlspecialchars($keyword); ?>"
                       class="flex-1 px-4 py-3 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-blue-300">
                <button type="submit" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                    ค้นหา
                </button>
            </form>
        </div>
    </section>

    <!-- Categories -->
    <section class="mb-8">
        <h2 class="text-xl font-bold mb-4">หมวดหมู่</h2>
        <div class="flex flex-wrap gap-2">
            <?php foreach ($categories as $category): ?>
                <a href="/category/<?php echo urlencode($category); ?>" 
                   class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm hover:bg-blue-200 transition">
                    <?php echo htmlspecialchars($category); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Filter & Sort -->
    <section class="mb-8 flex gap-4 items-center">
        <div class="flex-1">
            <label class="text-sm font-medium">เรียงลำดับ:</label>
            <form method="GET" action="/" class="mt-2 flex gap-2">
                <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                <select name="sort" onchange="this.form.submit()" class="px-4 py-2 border border-border rounded-lg">
                    <option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>ค่าเริ่มต้น</option>
                    <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>ราคา: ต่ำ - สูง</option>
                    <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>ราคา: สูง - ต่ำ</option>
                    <option value="discount" <?php echo $sort === 'discount' ? 'selected' : ''; ?>>ส่วนลดมากที่สุด</option>
                </select>
            </form>
        </div>
        <div class="text-sm text-gray-600">
            พบ <?php echo number_format($total); ?> รายการ
        </div>
    </section>

    <!-- Product Grid -->
    <section class="mb-12">
        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <p class="text-xl text-gray-600">ไม่พบสินค้า</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition overflow-hidden">
                        <div class="relative pb-full">
                            <img src="<?php echo htmlspecialchars($product['product_image'] ?? '/assets/images/placeholder.svg'); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 class="w-full h-48 object-cover">
                            <?php if ($product['product_discount_percentage'] > 0): ?>
                                <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-bold">
                                    -<?php echo intval($product['product_discount_percentage']); ?>%
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-sm mb-2 line-clamp-2">
                                <?php echo htmlspecialchars(truncateText($product['product_name'], 60)); ?>
                            </h3>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-yellow-400">★</span>
                                <span class="text-sm font-medium"><?php echo number_format($product['product_rating'], 1); ?></span>
                            </div>
                            <div class="mb-3">
                                <?php if ($product['product_discounted'] > 0): ?>
                                    <div class="text-lg font-bold text-red-600">
                                        <?php echo formatPrice($product['product_discounted']); ?>
                                    </div>
                                    <div class="text-sm text-gray-400 line-through">
                                        <?php echo formatPrice($product['product_price']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-lg font-bold">
                                        <?php echo formatPrice($product['product_price']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo htmlspecialchars($product['cloaked_url'] ?? $product['product_url']); ?>" 
                               target="_blank" rel="noopener noreferrer"
                               class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition text-center block">
                                ดูสินค้า
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <section class="mb-12 flex justify-center gap-2">
        <?php if ($page > 1): ?>
            <a href="/?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&sort=<?php echo urlencode($sort); ?>" 
               class="px-4 py-2 border border-border rounded-lg hover:bg-gray-100 transition">
                ← ก่อนหน้า
            </a>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold">
                    <?php echo $i; ?>
                </button>
            <?php else: ?>
                <a href="/?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&sort=<?php echo urlencode($sort); ?>" 
                   class="px-4 py-2 border border-border rounded-lg hover:bg-gray-100 transition">
                    <?php echo $i; ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="/?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&sort=<?php echo urlencode($sort); ?>" 
               class="px-4 py-2 border border-border rounded-lg hover:bg-gray-100 transition">
                ถัดไป →
            </a>
        <?php endif; ?>
    </section>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, 'หน้าแรก - ' . $config['siteName']);
?>
