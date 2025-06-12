<?php

namespace Controllers;

require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Models/TypeAccessory.php';
require_once __DIR__ . '/BaseController.php';

/**
 * ProductController
 * Handles product display, search, and management for mobile phones and accessories
 */
class ProductController extends BaseController
{
    
    // ========================================
    // PUBLIC PRODUCT DISPLAY METHODS
    // ========================================

    /**
     * Display all products (for admin view)
     */
    public function index(){
        $this->requireAdmin();

        $productModel = new \Models\Product();
        $products = $productModel->getAll();

        $this->loadView('admin/products/index', ['products' => $products]);
    }

    /**
     * Display mobile phones catalog
     */
    public function phoneCatalog(){
        $productModel = new \Models\Product();
        $categoryModel = new \Models\Category();
        
        // Get phones category
        $categories = $categoryModel->getAll();
        $phoneCategoryId = null;
        
        foreach ($categories as $category) {
            if (stripos($category['name'], 'móvil') !== false || 
                stripos($category['name'], 'teléfono') !== false) {
                $phoneCategoryId = $category['id'];
                break;
            }
        }
        
        if (!$phoneCategoryId) {
            $this->setErrorMessage('Phone category not found');
            $this->redirect('home', 'index');
            return;
        }

        // Get all phones
        $phones = $productModel->getByCategory($phoneCategoryId);
        
        $viewData = [
            'phones' => $phones,
            'categoryName' => 'Móviles'
        ];

        $this->loadView('products/phones', $viewData);
    }

    /**
     * Display accessories catalog with filter options
     */
    public function accessoriesCatalog(){
        $productModel = new \Models\Product();
        $categoryModel = new \Models\Category();
        $accessoryTypeModel = new \Models\AccessoryType();
        
        // Get accessories category
        $categories = $categoryModel->getAll();
        $accessoryCategoryId = null;
        
        foreach ($categories as $category) {
            if (stripos($category['name'], 'accesorio') !== false) {
                $accessoryCategoryId = $category['id'];
                break;
            }
        }
        
        if (!$accessoryCategoryId) {
            $this->setErrorMessage('Accessories category not found');
            $this->redirect('home', 'index');
            return;
        }

        // Get filter parameters
        $typeFilter = $this->getGetData('type');
        
        // Get all accessory types for filter menu
        $accessoryTypes = $accessoryTypeModel->getAll();
        
        // Get accessories based on filter
        if ($typeFilter && is_numeric($typeFilter)) {
            $accessories = $productModel->getByAccessoryType($typeFilter);
        } else {
            $accessories = $productModel->getByCategory($accessoryCategoryId);
        }
        
        $viewData = [
            'accessories' => $accessories,
            'accessoryTypes' => $accessoryTypes,
            'selectedType' => $typeFilter,
            'categoryName' => 'Accesorios'
        ];

        $this->loadView('products/accessories', $viewData);
    }

    /**
     * Display detailed view of a single product
     */
    public function detailProd(){
        $productId = $this->getGetData('id');
        
        if (!$productId || !is_numeric($productId)) {
            $this->setErrorMessage('Invalid product ID');
            $this->goBack();
            return;
        }

        $productModel = new \Models\Product();
        $product = $productModel->getProductById($productId);
        
        if (!$product) {
            $this->setErrorMessage('Product not found');
            $this->goBack();
            return;
        }

        // Get related products (same category, different product)
        $relatedProducts = [];
        $categoryProducts = $productModel->getByCategory($productModel->getCategoryId());
        
        if ($categoryProducts) {
            $count = 0;
            foreach ($categoryProducts as $relatedProduct) {
                if ($relatedProduct['id'] != $productId && $relatedProduct['stock'] > 0 && $count < 4) {
                    $relatedProducts[] = $relatedProduct;
                    $count++;
                }
            }
        }

        $viewData = [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];

        $this->loadView('products/detail', $viewData);
    }

    /**
     * Search products by keyword
     */
    public function searchProduct(){
        $keyword = $this->getGetData('q');
        
        if (empty($keyword)) {
            $this->redirect('home', 'index');
            return;
        }

        $productModel = new \Models\Product();
        $results = $productModel->search($keyword);
        
        $viewData = [
            'results' => $results,
            'keyword' => $keyword,
            'resultCount' => $results ? count($results) : 0
        ];

        $this->loadView('products/search', $viewData);
    }

    // ========================================
    // ADMIN PRODUCT MANAGEMENT METHODS
    // ========================================

    /**
     * Show form to create new product
     */
    public function createProduct()
    {
        $this->requireAdmin();

        $categoryModel = new \Models\Category();
        $accessoryTypeModel = new \Models\AccessoryType();

        $categories = $categoryModel->getAll();
        $accessoryTypes = $accessoryTypeModel->getAll();
        
        $viewData = [
            'categories' => $categories,
            'accessoryTypes' => $accessoryTypes
        ];

        $this->loadView('admin/products/create', $viewData);
    }

    /**
     * Process new product creation
     */
    public function createProductSubmit()
    {
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('product', 'create');
            return;
        }

        $postData = $this->getPostData();
        
        // Validate required fields
        $requiredFields = ['name', 'description', 'price', 'stock', 'category_id'];
        $errors = $this->validateRequired($postData, $requiredFields);
        
        if (!empty($errors)) {
            $this->setErrorMessage('All fields are required');
            $this->redirect('product', 'create');
            return;
        }

        // Additional validation
        $price = floatval($postData['price']);
        $stock = intval($postData['stock']);
        $categoryId = intval($postData['category_id']);
        $accessoryTypeId = !empty($postData['accessory_type_id']) ? intval($postData['accessory_type_id']) : null;
        
        if ($price <= 0) {
            $this->setErrorMessage('Price must be greater than 0');
            $this->redirect('product', 'create');
            return;
        }
        
        if ($stock < 0) {
            $this->setErrorMessage('Stock cannot be negative');
            $this->redirect('product', 'create');
            return;
        }

        // Handle image upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $imageName = $this->handleImageUpload($_FILES['image']);
            if (!$imageName) {
                $this->redirect('product', 'create');
                return;
            }
        }

        // Create product
        $product = new \Models\Product();
        $product->setCategoryId($categoryId);
        $product->setAccessoryType($accessoryTypeId);
        $product->setName($postData['name']);
        $product->setDescription($postData['description']);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setImage($imageName);
        
        $saved = $product->saveDB();
        
        if ($saved) {
            $this->setSuccessMessage('Product created successfully');
            $this->redirect('product', 'index');
        } else {
            $this->setErrorMessage('Error creating product');
            $this->redirect('product', 'create');
        }
    }

    /**
     * Show form to edit existing product
     */
    public function editProduct()
    {
        $this->requireAdmin();
        
        $productId = $this->getGetData('id');
        
        if (!$productId || !is_numeric($productId)) {
            $this->setErrorMessage('Invalid product ID');
            $this->redirect('product', 'index');
            return;
        }

        $productModel = new \Models\Product();
        $product = $productModel->getProductById($productId);
        
        if (!$product) {
            $this->setErrorMessage('Product not found');
            $this->redirect('product', 'index');
            return;
        }

        $categoryModel = new \Models\Category();
        $accessoryTypeModel = new \Models\AccessoryType();
        
        $categories = $categoryModel->getAll();
        $accessoryTypes = $accessoryTypeModel->getAll();
        
        $viewData = [
            'product' => $product,
            'categories' => $categories,
            'accessoryTypes' => $accessoryTypes
        ];

        $this->loadView('admin/products/edit', $viewData);
    }

    /**
     * Process product update
     */
    public function updateProduct()
    {
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('product', 'index');
            return;
        }

        $postData = $this->getPostData();
        $productId = intval($postData['id'] ?? 0);
        
        if (!$productId) {
            $this->setErrorMessage('Invalid product ID');
            $this->redirect('product', 'index');
            return;
        }

        // Validate required fields
        $requiredFields = ['name', 'description', 'price', 'stock', 'category_id'];
        $errors = $this->validateRequired($postData, $requiredFields);
        
        if (!empty($errors)) {
            $this->setErrorMessage('All fields are required');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        // Additional validation
        $price = floatval($postData['price']);
        $stock = intval($postData['stock']);
        $categoryId = intval($postData['category_id']);
        $accessoryTypeId = !empty($postData['accessory_type_id']) ? intval($postData['accessory_type_id']) : null;
        
        if ($price <= 0) {
            $this->setErrorMessage('Price must be greater than 0');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }
        
        if ($stock < 0) {
            $this->setErrorMessage('Stock cannot be negative');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        // Get existing product
        $product = new \Models\Product();
        if (!$product->getProductById($productId)) {
            $this->setErrorMessage('Product not found');
            $this->redirect('product', 'index');
            return;
        }

        // Handle image upload if new image provided
        $imageName = $product->getImage();
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $newImageName = $this->handleImageUpload($_FILES['image']);
            if ($newImageName) {
                // Delete old image
                $this->deleteProductImage($imageName);
                $imageName = $newImageName;
            } else {
                $this->redirect('product', 'edit', ['id' => $productId]);
                return;
            }
        }

        // Update product
        $product->setId($productId);
        $product->setCategoryId($categoryId);
        $product->setAccessoryType($accessoryTypeId);
        $product->setName($postData['name']);
        $product->setDescription($postData['description']);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setImage($imageName);
        
        $updated = $product->updateDB();
        
        if ($updated) {
            $this->setSuccessMessage('Product updated successfully');
            $this->redirect('product', 'index');
        } else {
            $this->setErrorMessage('Error updating product');
            $this->redirect('product', 'edit', ['id' => $productId]);
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct()
    {
        $this->requireAdmin();
        
        if (!$this->isPost()) {
            $this->setErrorMessage('Invalid request method');
            $this->redirect('product', 'index');
            return;
        }

        $productId = $this->getPostData('id');
        
        if (!$productId || !is_numeric($productId)) {
            $this->setErrorMessage('Invalid product ID');
            $this->redirect('product', 'index');
            return;
        }

        $product = new \Models\Product();
        if (!$product->getProductById($productId)) {
            $this->setErrorMessage('Product not found');
            $this->redirect('product', 'index');
            return;
        }

        // Delete product
        $deleted = $product->delete();
        
        if ($deleted) {
            // Delete product image
            $this->deleteProductImage($product->getImage());
            $this->setSuccessMessage('Product deleted successfully');
        } else {
            $this->setErrorMessage('Error deleting product');
        }

        $this->redirect('product', 'index');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Handle image upload
     * @param array $file $_FILES array element
     * @return string|false Filename on success, false on failure
     */
    private function handleImageUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            $this->setErrorMessage('Invalid image format. Allowed: JPG, PNG, GIF');
            return false;
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            $this->setErrorMessage('Image size must be less than 5MB');
            return false;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
        
        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../assets/img/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }
        
        $this->setErrorMessage('Error uploading image');
        return false;
    }

    /**
     * Delete product image from server
     * @param string $filename Image filename
     */
    private function deleteProductImage($filename)
    {
        if (!empty($filename)) {
            $imagePath = __DIR__ . '/../assets/img/products/' . $filename;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }

    /**
     * Display mobile phones - simple alias for phoneCatalog
     */
    public function phones()
    {
        $this->phoneCatalog();
    }

    /**
     * Display accessories - simple alias for accessoriesCatalog  
     */
    public function accessories()
    {
        $this->accessoriesCatalog();
    }

    /**
     * Display products by category ID
     */
    public function categoryProducts(){
        $categoryId = $this->getGetData('category');
        
        if (!$categoryId || !is_numeric($categoryId)) {
            $this->setErrorMessage('Categoría no válida');
            $this->redirect('home', 'index');
            return;
        }

        $productModel = new \Models\Product();
        $categoryModel = new \Models\Category();
        
        // Get category info
        $category = $categoryModel->getById($categoryId);
        if (!$category) {
            $this->setErrorMessage('Categoría no encontrada');
            $this->redirect('home', 'index');
            return;
        }

        // Get products for this category
        $products = $productModel->getByCategory($categoryId);
        
        $viewData = [
            'products' => $products,
            'categoryName' => $category->getName(),
            'categoryId' => $categoryId
        ];

        $this->loadView('products/category', $viewData);
    }
}

?>