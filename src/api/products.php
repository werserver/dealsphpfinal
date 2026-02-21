<?php
/**
 * Products API Endpoints
 */

function handleProducts() {
    if (!isGet()) {
        return sendError('Method not allowed', 405);
    }

    $action = getQueryParam('action', 'list');
    $page = intval(getQueryParam('page', 1));
    $keyword = sanitizeInput(getQueryParam('keyword', ''));
    $category = sanitizeInput(getQueryParam('category', ''));
    $sort = sanitizeInput(getQueryParam('sort', 'default'));
    $limit = intval(getQueryParam('limit', 20));

    if ($action === 'search') {
        handleSearchProducts($keyword, $page, $limit, $sort);
    } elseif ($action === 'category') {
        handleCategoryProducts($category, $page, $limit, $sort);
    } elseif ($action === 'detail') {
        handleProductDetail();
    } else {
        handleListProducts($page, $limit, $sort);
    }
}

function handleListProducts($page = 1, $limit = 20, $sort = 'default') {
    $config = Config::get();
    $categories = $config['categories'] ?? [];
    
    $allProducts = [];
    
    // Load products from all CSV files
    foreach ($categories as $category) {
        $products = CsvParser::loadCsvFromFile($category);
        foreach ($products as $product) {
            $product['category_name'] = $category;
            $allProducts[] = $product;
        }
    }

    // Apply sorting
    $allProducts = applySorting($allProducts, $sort);

    // Paginate
    $total = count($allProducts);
    $offset = ($page - 1) * $limit;
    $products = array_slice($allProducts, $offset, $limit);

    // Apply URL cloaking
    $products = applyCloaking($products);

    sendSuccess([
        'data' => $products,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

function handleSearchProducts($keyword, $page = 1, $limit = 20, $sort = 'default') {
    if (empty($keyword)) {
        return sendError('Keyword is required');
    }

    $config = Config::get();
    $categories = $config['categories'] ?? [];
    
    $allProducts = [];
    $keyword_lower = strtolower($keyword);

    // Load and search products from all CSV files
    foreach ($categories as $category) {
        $products = CsvParser::loadCsvFromFile($category);
        foreach ($products as $product) {
            if (stripos($product['product_name'], $keyword) !== false ||
                stripos($product['product_shop_name'], $keyword) !== false) {
                $product['category_name'] = $category;
                $allProducts[] = $product;
            }
        }
    }

    // Apply sorting
    $allProducts = applySorting($allProducts, $sort);

    // Paginate
    $total = count($allProducts);
    $offset = ($page - 1) * $limit;
    $products = array_slice($allProducts, $offset, $limit);

    // Apply URL cloaking
    $products = applyCloaking($products);

    sendSuccess([
        'data' => $products,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

function handleCategoryProducts($category, $page = 1, $limit = 20, $sort = 'default') {
    if (empty($category)) {
        return sendError('Category is required');
    }

    $products = CsvParser::loadCsvFromFile($category);
    
    // Apply sorting
    $products = applySorting($products, $sort);

    // Paginate
    $total = count($products);
    $offset = ($page - 1) * $limit;
    $products = array_slice($products, $offset, $limit);

    // Apply URL cloaking
    $products = applyCloaking($products);

    sendSuccess([
        'data' => $products,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

function handleProductDetail() {
    $slug = sanitizeInput(getQueryParam('slug', ''));
    
    if (empty($slug)) {
        return sendError('Product slug is required');
    }

    $parsed = parseProductSlug($slug);
    $productId = $parsed['id'];

    // Search for product in all categories
    $config = Config::get();
    $categories = $config['categories'] ?? [];

    foreach ($categories as $category) {
        $products = CsvParser::loadCsvFromFile($category);
        foreach ($products as $product) {
            if ($product['product_id'] == $productId) {
                $product['category_name'] = $category;
                $product = applyCloakingToProduct($product);
                sendSuccess($product);
            }
        }
    }

    sendError('Product not found', 404);
}

function applySorting($products, $sort) {
    switch ($sort) {
        case 'price-asc':
            usort($products, function($a, $b) {
                $priceA = $a['product_discounted'] ?: $a['product_price'];
                $priceB = $b['product_discounted'] ?: $b['product_price'];
                return $priceA <=> $priceB;
            });
            break;
        case 'price-desc':
            usort($products, function($a, $b) {
                $priceA = $a['product_discounted'] ?: $a['product_price'];
                $priceB = $b['product_discounted'] ?: $b['product_price'];
                return $priceB <=> $priceA;
            });
            break;
        case 'discount':
            usort($products, function($a, $b) {
                return $b['product_discount_percentage'] <=> $a['product_discount_percentage'];
            });
            break;
        case 'rating':
            usort($products, function($a, $b) {
                return $b['product_rating'] <=> $a['product_rating'];
            });
            break;
        case 'sold':
            usort($products, function($a, $b) {
                return $b['product_sold'] <=> $a['product_sold'];
            });
            break;
    }
    return $products;
}

function applyCloaking($products) {
    $config = Config::get();
    $token = $config['cloakingToken'] ?? UrlBuilder::getDefaultToken();
    $baseUrl = $config['cloakingBaseUrl'] ?? UrlBuilder::getDefaultCloakingUrl();

    foreach ($products as &$product) {
        $product = applyCloakingToProduct($product, $token, $baseUrl);
    }
    return $products;
}

function applyCloakingToProduct($product, $token = null, $baseUrl = null) {
    $config = Config::get();
    $token = $token ?? ($config['cloakingToken'] ?? UrlBuilder::getDefaultToken());
    $baseUrl = $baseUrl ?? ($config['cloakingBaseUrl'] ?? UrlBuilder::getDefaultCloakingUrl());

    if (!empty($product['product_url'])) {
        $product['cloaked_url'] = UrlBuilder::buildCloakedUrl($product['product_url'], $token, $baseUrl);
    }
    return $product;
}
?>
