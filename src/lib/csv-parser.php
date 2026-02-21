<?php
/**
 * CSV Parser Library
 * Handles parsing and managing CSV product data
 */

class CsvParser {
    private static $products_cache = [];
    private static $cache_time = [];

    public static function parseProductCsv($csvContent) {
        $lines = explode("\n", trim($csvContent));
        $products = [];
        
        // Skip header
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;
            
            // Parse CSV line - handle quoted fields
            $fields = self::parseCsvLine($line);
            if (count($fields) < 15) continue;
            
            $product = [
                'product_id' => $fields[0] ?? '',
                'product_url' => $fields[1] ?? '',
                'product_name' => $fields[2] ?? '',
                'product_price' => floatval($fields[3] ?? 0),
                'product_discounted' => floatval($fields[4] ?? 0),
                'product_min_price' => floatval($fields[5] ?? 0),
                'product_max_price' => floatval($fields[6] ?? 0),
                'product_sold' => intval($fields[7] ?? 0),
                'product_discount_percentage' => floatval(str_replace('%', '', $fields[8] ?? '0')),
                'product_discount_text' => $fields[9] ?? '',
                'product_shop_id' => $fields[10] ?? '',
                'product_shop_name' => $fields[11] ?? '',
                'product_shop_location' => $fields[12] ?? '',
                'product_rating' => floatval($fields[13] ?? 0),
                'product_review_count' => intval($fields[14] ?? 0),
                'category_name' => 'สินค้าแนะนำ' // Default category
            ];
            
            $products[] = $product;
        }
        
        return $products;
    }

    private static function parseCsvLine($line) {
        $fields = [];
        $current = '';
        $in_quotes = false;
        
        for ($i = 0; $i < strlen($line); $i++) {
            $char = $line[$i];
            
            if ($char === '"') {
                $in_quotes = !$in_quotes;
            } elseif ($char === ',' && !$in_quotes) {
                $fields[] = trim($current, '"');
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        $fields[] = trim($current, '"');
        return $fields;
    }

    public static function loadCsvFromFile($category) {
        $csv_path = STORAGE_PATH . '/csv/' . sanitizeFilename($category) . '.csv';
        
        if (file_exists($csv_path)) {
            $content = file_get_contents($csv_path);
            return self::parseProductCsv($content);
        }
        
        return [];
    }

    public static function saveCsvToFile($category, $csvContent) {
        // Ensure CSV directory exists
        $csv_dir = STORAGE_PATH . '/csv';
        if (!is_dir($csv_dir)) {
            mkdir($csv_dir, 0755, true);
        }
        
        $csv_path = $csv_dir . '/' . sanitizeFilename($category) . '.csv';
        file_put_contents($csv_path, $csvContent);
        
        // Clear cache
        unset(self::$products_cache[$category]);
        unset(self::$cache_time[$category]);
        
        return true;
    }

    public static function deleteCsvFile($category) {
        $csv_path = STORAGE_PATH . '/csv/' . sanitizeFilename($category) . '.csv';
        
        if (file_exists($csv_path)) {
            unlink($csv_path);
            unset(self::$products_cache[$category]);
            unset(self::$cache_time[$category]);
            return true;
        }
        
        return false;
    }

    public static function getCsvFiles() {
        $csv_dir = STORAGE_PATH . '/csv';
        $files = [];
        
        if (is_dir($csv_dir)) {
            foreach (scandir($csv_dir) as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                    $files[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
        }
        
        return $files;
    }
}

function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
}
?>
