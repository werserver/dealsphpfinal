<?php
/**
 * Category Page - Products by Category
 */

$config = Config::get();

// Get category from URL
$params = array_filter(explode('/', trim($_SERVER['REQUEST_URI'], '/')));
$category = end($params);
$category = urldecode($category);

// Get current page
$page = intval(getQueryParam('page', 1));
$sort = sanitizeInput(getQueryParam('sort', 'default'));

// Load products for this category
$products = CsvParser::loadCsvFromFile($category);

// Apply sorting
if ($sort === 'price-asc') {
    usort($products, function($a, $b) {
        $priceA = $a['product_discounted'] ?: $a['product_price'];
        $priceB = $b['product_discounted'] ?: $b['product_price'];
        return $priceA <=> $priceB;
    });
} elseif ($sort === 'price-desc') {
    usort($products, function($a, $b) {
        $priceA = $a['product_discounted'] ?: $a['product_price'];
        $priceB = $b['product_discounted'] ?: $b['product_price'];
        return $priceB <=> $priceA;
    });
} elseif ($sort === 'discount') {
    usort($products, function($a, $b) {
        return $b['product_discount_percentage'] <=> $a['product_discount_percentage'];
    });
}

// Paginate
$limit = 20;
$total = count($products);
$offset = ($page - 1) * $limit;
$products = array_slice($products, $offset, $limit);
$totalPages = ceil($total / $limit);

// Apply URL cloaking
foreach ($products as &$product) {
    $token = $config['cloakingToken'] ?? UrlBuilder::getDefaultToken();
    $baseUrl = $config['cloakingBaseUrl'] ?? UrlBuilder::getDefaultCloakingUrl();
    if (!empty($product['product_url'])) {
        $product['cloaked_url'] = UrlBuilder::buildCloakedUrl($product['product_url'], $token, $baseUrl);
    }
}

// Generate content
ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="mb-6 text-sm text-gray-600">
        <a href="/" class="hover:text-blue-600">หน้าแรก</a>
        <span class="mx-2">/</span>
        <span><?php echo htmlspecialchars($category); ?></span>
    </div>

    <!-- Category Header -->
    <h1 class="text-4xl font-bold mb-2"><?php echo htmlspecialchars($category); ?></h1>
    <p class="text-gray-600 mb-8">พบ <?php echo number_format($total); ?> รายการ</p>

    <!-- Filter & Sort -->
    <section class="mb-8 flex gap-4 items-center">
        <div class="flex-1">
            <label class="text-sm font-medium">เรียงลำดับ:</label>
            <select id="sortSelect" onchange="handleSort()" class="mt-2 px-4 py-2 border border-border rounded-lg">
                <option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>ค่าเริ่มต้น</option>
                <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>ราคา: ต่ำ - สูง</option>
                <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>ราคา: สูง - ต่ำ</option>
                <option value="discount" <?php echo $sort === 'discount' ? 'selected' : ''; ?>>ส่วนลดมากที่สุด</option>
            </select>
        </div>
    </section>

    <!-- Product Grid -->
    <section class="mb-12">
        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <p class="text-xl text-gray-600">ไม่พบสินค้าในหมวดหมู่นี้</p>
                <a href="/" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    ไปหน้าแรก
                </a>
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
                                <div class="flex items-center gap-1">
                                    <span class="text-yellow-400">★</span>
                                    <span class="text-sm font-medium"><?php echo number_format($product['product_rating'], 1); ?></span>
                                </div>
                                <span class="text-xs text-gray-500">(<?php echo number_format($product['product_review_count']); ?>)</span>
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
                            <div class="text-xs text-gray-600 mb-3">
                                ร้าน: <?php echo htmlspecialchars($product['product_shop_name']); ?>
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
            <a href="/category/<?php echo urlencode($category); ?>?page=<?php echo $page - 1; ?>&sort=<?php echo urlencode($sort); ?>" 
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
                <a href="/category/<?php echo urlencode($category); ?>?page=<?php echo $i; ?>&sort=<?php echo urlencode($sort); ?>" 
                   class="px-4 py-2 border border-border rounded-lg hover:bg-gray-100 transition">
                    <?php echo $i; ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="/category/<?php echo urlencode($category); ?>?page=<?php echo $page + 1; ?>&sort=<?php echo urlencode($sort); ?>" 
               class="px-4 py-2 border border-border rounded-lg hover:bg-gray-100 transition">
                ถัดไป →
            </a>
        <?php endif; ?>
    </section>
    <?php endif; ?>
</div>

<script>
function handleSort() {
    const sort = document.getElementById('sortSelect').value;
    const category = '<?php echo urlencode($category); ?>';
    window.location.href = '/category/' + category + '?sort=' + encodeURIComponent(sort);
}
</script>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, htmlspecialchars($category));
?>
