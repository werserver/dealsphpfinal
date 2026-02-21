<?php
/**
 * Product Detail Page
 */

$config = Config::get();

// Get product slug from URL
$params = array_filter(explode('/', trim($_SERVER['REQUEST_URI'], '/')));
$slug = end($params);

// Parse slug to get product ID
$parsed = parseProductSlug($slug);
$productId = $parsed['id'];

// Find product in all categories
$product = null;
$categories = $config['categories'] ?? [];

foreach ($categories as $category) {
    $products = CsvParser::loadCsvFromFile($category);
    foreach ($products as $p) {
        if ($p['product_id'] == $productId) {
            $p['category_name'] = $category;
            $product = $p;
            break 2;
        }
    }
}

if (!$product) {
    http_response_code(404);
    require_once SRC_PATH . '/pages/404.php';
    exit;
}

// Apply URL cloaking
$token = $config['cloakingToken'] ?? UrlBuilder::getDefaultToken();
$baseUrl = $config['cloakingBaseUrl'] ?? UrlBuilder::getDefaultCloakingUrl();
if (!empty($product['product_url'])) {
    $product['cloaked_url'] = UrlBuilder::buildCloakedUrl($product['product_url'], $token, $baseUrl);
}

// Generate content
ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="mb-6 text-sm text-gray-600">
        <a href="/" class="hover:text-blue-600">หน้าแรก</a>
        <span class="mx-2">/</span>
        <a href="/category/<?php echo urlencode($product['category_name']); ?>" class="hover:text-blue-600">
            <?php echo htmlspecialchars($product['category_name']); ?>
        </a>
        <span class="mx-2">/</span>
        <span><?php echo htmlspecialchars(truncateText($product['product_name'], 50)); ?></span>
    </div>

    <!-- Product Detail -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Product Image -->
        <div>
            <div class="bg-gray-100 rounded-lg overflow-hidden mb-4">
                <img src="<?php echo htmlspecialchars($product['product_image'] ?? '/assets/images/placeholder.svg'); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                     class="w-full h-96 object-cover">
            </div>
            <?php if (!empty($product['product_images'])): ?>
                <div class="grid grid-cols-4 gap-2">
                    <!-- Additional images would go here -->
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold mb-4">
                <?php echo htmlspecialchars($product['product_name']); ?>
            </h1>

            <!-- Rating -->
            <div class="flex items-center gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-2xl text-yellow-400">★</span>
                    <span class="text-xl font-bold"><?php echo number_format($product['product_rating'], 1); ?></span>
                </div>
                <span class="text-gray-600"><?php echo number_format($product['product_review_count']); ?> รีวิว</span>
                <span class="text-gray-600"><?php echo number_format($product['product_sold']); ?> ขายแล้ว</span>
            </div>

            <!-- Price -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <?php if ($product['product_discounted'] > 0): ?>
                    <div class="flex items-center gap-4 mb-2">
                        <div class="text-4xl font-bold text-red-600">
                            <?php echo formatPrice($product['product_discounted']); ?>
                        </div>
                        <div class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            -<?php echo intval($product['product_discount_percentage']); ?>%
                        </div>
                    </div>
                    <div class="text-lg text-gray-400 line-through mb-4">
                        <?php echo formatPrice($product['product_price']); ?>
                    </div>
                <?php else: ?>
                    <div class="text-4xl font-bold mb-4">
                        <?php echo formatPrice($product['product_price']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($product['product_discount_text'])): ?>
                    <p class="text-sm text-gray-700">
                        <strong>โปรโมชั่น:</strong> <?php echo htmlspecialchars($product['product_discount_text']); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Shop Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600 mb-2">ร้านค้า</p>
                <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($product['product_shop_name']); ?></h3>
                <p class="text-sm text-gray-600">📍 <?php echo htmlspecialchars($product['product_shop_location']); ?></p>
            </div>

            <!-- Price Range -->
            <?php if ($product['product_min_price'] > 0 || $product['product_max_price'] > 0): ?>
                <div class="mb-6">
                    <p class="text-sm font-medium mb-2">ช่วงราคา</p>
                    <p class="text-gray-600">
                        <?php echo formatPrice($product['product_min_price']); ?> - <?php echo formatPrice($product['product_max_price']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex gap-4 mb-6">
                <a href="<?php echo htmlspecialchars($product['cloaked_url'] ?? $product['product_url']); ?>" 
                   target="_blank" rel="noopener noreferrer"
                   class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition text-center">
                    ซื้อสินค้า
                </a>
                <button onclick="addToWishlist(<?php echo json_encode($product); ?>)" 
                        class="px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-lg font-bold hover:bg-blue-50 transition">
                    ❤️ เพิ่มรายการโปรด
                </button>
            </div>

            <!-- Description -->
            <?php if (!empty($product['product_description'])): ?>
                <div class="bg-white border border-border rounded-lg p-4">
                    <h3 class="font-bold mb-2">รายละเอียด</h3>
                    <p class="text-gray-700 text-sm">
                        <?php echo htmlspecialchars(truncateText($product['product_description'], 500)); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">สินค้าที่เกี่ยวข้อง</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            // Get related products from same category
            $relatedProducts = CsvParser::loadCsvFromFile($product['category_name']);
            $relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
                return $p['product_id'] != $productId;
            });
            $relatedProducts = array_slice($relatedProducts, 0, 4);

            foreach ($relatedProducts as $related):
                $relatedToken = $config['cloakingToken'] ?? UrlBuilder::getDefaultToken();
                $relatedBaseUrl = $config['cloakingBaseUrl'] ?? UrlBuilder::getDefaultCloakingUrl();
                $related['cloaked_url'] = UrlBuilder::buildCloakedUrl($related['product_url'], $relatedToken, $relatedBaseUrl);
            ?>
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition overflow-hidden">
                    <div class="relative pb-full">
                        <img src="<?php echo htmlspecialchars($related['product_image'] ?? '/assets/images/placeholder.svg'); ?>" 
                             alt="<?php echo htmlspecialchars($related['product_name']); ?>"
                             class="w-full h-48 object-cover">
                        <?php if ($related['product_discount_percentage'] > 0): ?>
                            <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-bold">
                                -<?php echo intval($related['product_discount_percentage']); ?>%
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-sm mb-2 line-clamp-2">
                            <?php echo htmlspecialchars(truncateText($related['product_name'], 60)); ?>
                        </h3>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-yellow-400">★</span>
                            <span class="text-sm font-medium"><?php echo number_format($related['product_rating'], 1); ?></span>
                        </div>
                        <div class="mb-3">
                            <?php if ($related['product_discounted'] > 0): ?>
                                <div class="text-lg font-bold text-red-600">
                                    <?php echo formatPrice($related['product_discounted']); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-lg font-bold">
                                    <?php echo formatPrice($related['product_price']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo htmlspecialchars($related['cloaked_url']); ?>" 
                           target="_blank" rel="noopener noreferrer"
                           class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition text-center block">
                            ดูสินค้า
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<script>
function addToWishlist(product) {
    window.addToWishlist(product);
}
</script>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, htmlspecialchars($product['product_name']));
?>
