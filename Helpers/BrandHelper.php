<?php

namespace Helpers;

/**
 * BrandHelper
 * Handles intelligent brand detection from product names
 */
class BrandHelper
{
    /**
     * Extract brand from product name using intelligent detection
     * @param string $productName The product name
     * @return string The detected brand key
     */
    public static function detectBrand($productName)
    {
        $name = strtolower(trim($productName));

        // iPhone detection
        if (strpos($name, 'iphone') === 0) {
            return 'iphone';
        }

        // Samsung detection (Samsung or Galaxy)
        if (strpos($name, 'samsung') !== false || strpos($name, 'galaxy') !== false) {
            return 'samsung';
        }

        // Xiaomi detection (Xiaomi or Redmi)
        if (strpos($name, 'xiaomi') !== false || strpos($name, 'redmi') !== false) {
            return 'xiaomi';
        }

        // Huawei detection
        if (strpos($name, 'huawei') !== false) {
            return 'huawei';
        }

        // Google Pixel detection
        if (strpos($name, 'pixel') !== false || strpos($name, 'google') !== false) {
            return 'google';
        }

        // Oppo detection
        if (strpos($name, 'oppo') !== false) {
            return 'oppo';
        }

        // Default for unrecognized brands
        return 'other';
    }

    /**
     * Get all available brands from mobile products
     * @param array $phones Array of phone products
     * @return array Array of unique brands with their display names
     */
    public static function getAvailableBrands($phones)
    {
        $brands = [];
        $brandLabels = self::getBrandLabels();

        if (!empty($phones)) {
            foreach ($phones as $phone) {
                $brand = self::detectBrand($phone['name']);
                if (!in_array($brand, $brands)) {
                    $brands[] = $brand;
                }
            }
        }

        // Sort brands alphabetically by display name
        usort($brands, function ($a, $b) use ($brandLabels) {
            return strcmp($brandLabels[$a] ?? $a, $brandLabels[$b] ?? $b);
        });

        return $brands;
    }

    /**
     * Get brand display labels
     * @return array Associative array of brand keys and display names
     */
    public static function getBrandLabels()
    {
        return [
            'iphone' => 'iPhone',
            'samsung' => 'Samsung',
            'xiaomi' => 'Xiaomi',
            'huawei' => 'Huawei',
            'google' => 'Google Pixel',
            'oppo' => 'Oppo',
            'other' => 'Otros'
        ];
    }

    /**
     * Get brand display name
     * @param string $brandKey The brand key
     * @return string The display name
     */
    public static function getBrandDisplayName($brandKey)
    {
        $labels = self::getBrandLabels();
        return $labels[$brandKey] ?? ucfirst($brandKey);
    }
}
