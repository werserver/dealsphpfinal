<?php
/**
 * Configuration Management Library
 * Handles loading and saving site configuration
 */

class Config {
    private static $config = null;
    private static $config_file = null;

    public static function init() {
        self::$config_file = STORAGE_PATH . '/config.json';
        self::load();
    }

    public static function load() {
        if (file_exists(self::$config_file)) {
            $json = file_get_contents(self::$config_file);
            self::$config = json_decode($json, true);
        } else {
            self::$config = self::getDefaults();
            self::save();
        }
    }

    public static function get($key = null) {
        if (self::$config === null) {
            self::load();
        }
        
        if ($key === null) {
            return self::$config;
        }
        
        return self::$config[$key] ?? null;
    }

    public static function set($key, $value) {
        if (self::$config === null) {
            self::load();
        }
        self::$config[$key] = $value;
    }

    public static function save() {
        if (self::$config === null) {
            self::load();
        }
        
        // Ensure storage directory exists
        if (!is_dir(STORAGE_PATH)) {
            mkdir(STORAGE_PATH, 0755, true);
        }
        
        $json = json_encode(self::$config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(self::$config_file, $json);
        return true;
    }

    public static function getDefaults() {
        return [
            'siteName' => 'ThaiDeals',
            'dataSource' => 'csv',
            'csvFilePath' => '/data/products.csv',
            'categories' => [
                'สินค้าแนะนำ',
                'ดีลเด็ด',
                'ของใช้ในบ้าน',
                'แฟชั่น',
                'อิเล็กทรอนิกส์'
            ],
            'keywords' => [
                'โปรโมชั่น',
                'ลดราคา',
                'สินค้ายอดนิยม',
                'ของใช้ในบ้าน',
                'แฟชั่น'
            ],
            'selectedAdvertisers' => [],
            'enableFlashSale' => true,
            'enableAiReviews' => false,
            'defaultCurrency' => 'THB',
            'themeColor' => 'light',
            'cloakingBaseUrl' => 'https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB',
            'cloakingToken' => 'QlpXZyCqMylKUjZiYchwB'
        ];
    }
}

// Initialize config on load
Config::init();
?>
