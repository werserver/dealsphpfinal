<?php
/**
 * URL Builder Library
 * Handles URL cloaking and tracking
 */

class UrlBuilder {
    const DEFAULT_CLOAKING_BASE_URL = 'https://goeco.mobi/?token=QlpXZyCqMylKUjZiYchwB';
    const DEFAULT_CLOAKING_TOKEN = 'QlpXZyCqMylKUjZiYchwB';

    public static function buildCloakedUrl($productUrl, $token = null, $customBaseUrl = null) {
        if (empty($productUrl)) {
            return '';
        }

        // Use custom base URL if provided, otherwise use default
        $baseUrl = $customBaseUrl ?? self::DEFAULT_CLOAKING_BASE_URL;
        $activeToken = $token ?? self::DEFAULT_CLOAKING_TOKEN;

        // If base URL contains token parameter, use it
        if (strpos($baseUrl, '?token=') !== false) {
            $encodedUrl = urlencode($productUrl);
            // Remove any existing url parameter
            $base = explode('&url=', $baseUrl)[0];
            return $base . '&url=' . $encodedUrl . '&source=api_product';
        }

        // Fallback: construct URL with token
        if ($activeToken) {
            $encodedUrl = urlencode($productUrl);
            return 'https://goeco.mobi/?token=' . $activeToken . '&url=' . $encodedUrl . '&source=api_product';
        }

        return $productUrl;
    }

    public static function getDefaultCloakingUrl() {
        return self::DEFAULT_CLOAKING_BASE_URL;
    }

    public static function getDefaultToken() {
        return self::DEFAULT_CLOAKING_TOKEN;
    }
}
?>
