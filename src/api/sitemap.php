<?php
/**
 * Sitemap Generation
 */

function generateSitemap() {
    $config = Config::get();
    $baseUrl = getBaseUrl();
    $categories = $config['categories'] ?? [];

    // Start XML
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Add main pages
    $pages = [
        '/',
        '/wishlist',
        '/about',
        '/contact'
    ];

    foreach ($pages as $page) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($baseUrl . $page) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>daily</changefreq>' . "\n";
        $xml .= '    <priority>0.8</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }

    // Add category pages
    foreach ($categories as $category) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/category/' . urlencode($category)) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>daily</changefreq>' . "\n";
        $xml .= '    <priority>0.7</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        // Add product pages
        $products = CsvParser::loadCsvFromFile($category);
        foreach (array_slice($products, 0, 1000) as $product) { // Limit to 1000 per category
            $slug = getProductSlug($product['product_name'], $product['product_id']);
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/product/' . $slug) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
    }

    $xml .= '</urlset>';

    echo $xml;
}
?>
