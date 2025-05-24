<?php

namespace Controllers;

require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/BaseController.php';

/**
 * HomeController
 * Handles the homepage and featured products.
 */
class HomeController extends BaseController
{
    /**
     * Display main homepage with featured products
     * Shows 4 newest mobile phones and 3 newest accessories with stock
     */
    public function index()
    {
        $productModel = new \Models\Product();
        $categoryModel = new \Models\Category();
        
        // Get all categories to find phones and accessories
        $categories = $categoryModel->getAll();
        
        // Find the category IDs for phones and accessories
        $phonesCategoryId = null;
        $accessoriesCategoryId = null;
        
        foreach ($categories as $category) {
            // Look for phones category (could be "Móviles", "Teléfonos", etc.)
            if (stripos($category['name'], 'móvil') !== false || 
                stripos($category['name'], 'teléfono') !== false) {
                $phonesCategoryId = $category['id'];
            }
            // Look for accessories category
            if (stripos($category['name'], 'accesorio') !== false) {
                $accessoriesCategoryId = $category['id'];
            }
        }
        
        // Get 4 newest phones with stock
        $featuredPhones = [];
        if ($phonesCategoryId) {
            $phones = $productModel->getByCategory($phonesCategoryId);
            if ($phones) {
                $count = 0;
                foreach ($phones as $phone) {
                    if ($phone['stock'] > 0 && $count < 4) {
                        $featuredPhones[] = $phone;
                        $count++;
                    }
                }
            }
        }
        
        // Get 3 newest accessories with stock
        $featuredAccessories = [];
        if ($accessoriesCategoryId) {
            $accessories = $productModel->getByCategory($accessoriesCategoryId);
            if ($accessories) {
                $count = 0;
                foreach ($accessories as $accessory) {
                    if ($accessory['stock'] > 0 && $count < 3) {
                        $featuredAccessories[] = $accessory;
                        $count++;
                    }
                }
            }
        }
        
        $viewData = [
            'featuredPhones' => $featuredPhones,
            'featuredAccessories' => $featuredAccessories
        ];
        
        $this->loadView('home/index', $viewData);
    }
}
?>