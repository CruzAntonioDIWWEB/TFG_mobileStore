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
     * Display all products (admin only) - UPDATED
     */
     public function index(){
        $this->requireAdmin();

        $productModel = new \Models\Product();
        $products = $productModel->getAll();

        $this->loadView('admin/products/index', ['products' => $products]);
    }

    /**
     * Show form to create a new product
     */
    public function create(){
        $this->requireAdmin();

        // Get categories and accessory types for dropdowns
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
     * Show form to edit an existing product
     */
    public function edit(){
        $this->requireAdmin();

        $productId = $this->getGetData('id');
        
        if (!$productId || !is_numeric($productId)) {
            $this->setErrorMessage('ID de producto inválido');
            $this->redirect('product', 'index');
            return;
        }

        $productModel = new \Models\Product();
        $product = $productModel->getProductById($productId);

        if (!$product) {
            $this->setErrorMessage('Producto no encontrado');
            $this->redirect('product', 'index');
            return;
        }

        // Get categories and accessory types for dropdowns
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

    // ========================================
    // PRODUCT CRUD OPERATIONS
    // ========================================

    /**
     * Save a new product
     */
    public function save(){
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('product', 'index');
            return;
        }

        $postData = $this->getPostData();
        
        // Basic validation
        $name = trim($postData['name'] ?? '');
        $description = trim($postData['description'] ?? '');
        $price = trim($postData['price'] ?? '');
        $stock = trim($postData['stock'] ?? '');
        $categoryId = trim($postData['category_id'] ?? '');

        if (empty($name) || empty($description) || empty($price) || empty($stock) || empty($categoryId)) {
            $this->setErrorMessage('Todos los campos obligatorios deben ser completados');
            $this->redirect('product', 'create');
            return;
        }

        // Validate numeric fields
        if (!is_numeric($price) || floatval($price) <= 0) {
            $this->setErrorMessage('El precio debe ser un número mayor que 0');
            $this->redirect('product', 'create');
            return;
        }

        if (!is_numeric($stock) || intval($stock) < 0) {
            $this->setErrorMessage('El stock debe ser un número mayor o igual a 0');
            $this->redirect('product', 'create');
            return;
        }

        if (!is_numeric($categoryId)) {
            $this->setErrorMessage('Categoría inválida');
            $this->redirect('product', 'create');
            return;
        }

        // Handle optional accessory type
        $accessoryTypeId = !empty($postData['accessory_type_id']) ? intval($postData['accessory_type_id']) : null;

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
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice(floatval($price));
        $product->setStock(intval($stock));
        $product->setCategoryId(intval($categoryId));
        $product->setAccessoryType($accessoryTypeId);
        $product->setImage($imageName);

        $saved = $product->saveDB(); // Use saveDB method to match your project pattern

        if ($saved) {
            $this->setSuccessMessage('Producto creado exitosamente');
            $this->redirect('product', 'index');
        } else {
            $this->setErrorMessage('Error al crear el producto');
            $this->redirect('product', 'create');
        }
    }

    /**
     * Update an existing product
     */
    public function update(){
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('product', 'index');
            return;
        }

        $postData = $this->getPostData();
        $productId = intval($postData['id'] ?? 0);

        if (!$productId) {
            $this->setErrorMessage('ID de producto inválido');
            $this->redirect('product', 'index');
            return;
        }

        // Basic validation
        $name = trim($postData['name'] ?? '');
        $description = trim($postData['description'] ?? '');
        $price = trim($postData['price'] ?? '');
        $stock = trim($postData['stock'] ?? '');
        $categoryId = trim($postData['category_id'] ?? '');

        if (empty($name) || empty($description) || empty($price) || empty($stock) || empty($categoryId)) {
            $this->setErrorMessage('Todos los campos obligatorios deben ser completados');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        // Validate numeric fields
        if (!is_numeric($price) || floatval($price) <= 0) {
            $this->setErrorMessage('El precio debe ser un número mayor que 0');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        if (!is_numeric($stock) || intval($stock) < 0) {
            $this->setErrorMessage('El stock debe ser un número mayor o igual a 0');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        if (!is_numeric($categoryId)) {
            $this->setErrorMessage('Categoría inválida');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        // Check if product exists and get current image
        $productModel = new \Models\Product();
        $existingProductResult = $productModel->getProductById($productId);

        if (!$existingProductResult) {
            $this->setErrorMessage('Producto no encontrado');
            $this->redirect('product', 'index');
            return;
        }

        // Handle the current image - work with both object and array returns
        $currentImage = null;
        if (is_object($existingProductResult)) {
            $currentImage = $existingProductResult->getImage();
        } elseif (is_array($existingProductResult)) {
            $currentImage = $existingProductResult['image'] ?? null;
        }

        // Handle optional accessory type
        $accessoryTypeId = !empty($postData['accessory_type_id']) ? intval($postData['accessory_type_id']) : null;

        // Handle image upload
        $imageName = $currentImage; // Keep existing image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $newImageName = $this->handleImageUpload($_FILES['image']);
            if ($newImageName) {
                // Delete old image if it exists
                if ($imageName) {
                    $this->deleteImage($imageName);
                }
                $imageName = $newImageName;
            } else {
                $this->redirect('product', 'edit', ['id' => $productId]);
                return;
            }
        }

        // Create a new Product instance for updating
        $product = new \Models\Product();
        $product->setId($productId);
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice(floatval($price));
        $product->setStock(intval($stock));
        $product->setCategoryId(intval($categoryId));
        $product->setAccessoryType($accessoryTypeId);
        $product->setImage($imageName);

        // Debug: Let's see what values we're trying to update
        error_log("DEBUG - Updating product ID: " . $productId);
        error_log("DEBUG - New name: " . $name);
        error_log("DEBUG - New price: " . $price);
        error_log("DEBUG - New stock: " . $stock);

        $updated = $product->updateDB();

        if ($updated) {
            $this->setSuccessMessage('Producto actualizado exitosamente');
            $this->redirect('product', 'index');
        } else {
            $this->setErrorMessage('Error al actualizar el producto');
            $this->redirect('product', 'edit', ['id' => $productId]);
        }
    }

    /**
     * Delete a product
     */
    public function delete(){
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('product', 'index');
            return;
        }

        $productId = intval($this->getPostData('id') ?? 0);

        if (!$productId) {
            $this->setErrorMessage('ID de producto inválido');
            $this->redirect('product', 'index');
            return;
        }

        // Check if product exists and get current image
        $productModel = new \Models\Product();
        $existingProductResult = $productModel->getProductById($productId);

        if (!$existingProductResult) {
            $this->setErrorMessage('Producto no encontrado');
            $this->redirect('product', 'index');
            return;
        }

        // Handle the current image - work with both object and array returns
        $currentImage = null;
        if (is_object($existingProductResult)) {
            $currentImage = $existingProductResult->getImage();
        } elseif (is_array($existingProductResult)) {
            $currentImage = $existingProductResult['image'] ?? null;
        }

        // Delete product image if it exists (before deleting the product)
        if ($currentImage) {
            $this->deleteImage($currentImage);
        }

        // Create a new Product instance for deletion
        $product = new \Models\Product();
        $product->setId($productId);
        $deleted = $product->delete();

        if ($deleted) {
            $this->setSuccessMessage('Producto eliminado exitosamente');
        } else {
            $this->setErrorMessage('Error al eliminar el producto');
        }

        $this->redirect('product', 'index');
    }

    // ========================================
    // IMAGE HANDLING HELPER METHODS
    // ========================================

    /**
     * Handle image upload
     * @param array $file The uploaded file from $_FILES
     * @return string|false The filename on success, false on failure
     */
    private function handleImageUpload($file){
        // Define upload directory (same as your project structure)
        $uploadDir = __DIR__ . '/../assets/img/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedTypes)) {
            $this->setErrorMessage('Tipo de archivo no válido. Solo se permiten: jpg, jpeg, png, gif');
            return false;
        }

        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $maxSize) {
            $this->setErrorMessage('El archivo es demasiado grande. Tamaño máximo: 5MB');
            return false;
        }

        // Generate unique filename
        $fileName = uniqid('product_') . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $fileName;
        } else {
            $this->setErrorMessage('Error al subir la imagen');
            return false;
        }
    }

    /**
     * Delete an image file
     * @param string $imageName The image filename to delete
     * @return bool Success/failure
     */
    private function deleteImage($imageName){
        if (empty($imageName)) {
            return true;
        }

        $imagePath = __DIR__ . '/../assets/img/products/' . $imageName;
        
        if (file_exists($imagePath)) {
            return unlink($imagePath);
        }
        
        return true; // File doesn't exist, consider it deleted
    }


    // ========================================
    // COMPATIBILITY/ALIAS METHODS
    // ========================================

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

        // Ensure products is always an array, even if empty
        if (!$products || !is_array($products)) {
            $products = [];
        }
        
        $viewData = [
            'products' => $products,
            'categoryName' => $category->getName(),
            'categoryId' => $categoryId
        ];

        $this->loadView('products/category', $viewData);
    }

    // ========================================
    // DEPRECATED/LEGACY METHODS (kept for compatibility)
    // ========================================

    public function createProduct() { $this->create(); }
    public function createProductSubmit() { $this->save(); }
    public function editProduct() { $this->edit(); }
    public function updateProduct() { $this->update(); }
    public function deleteProduct() { $this->delete(); }
    private function deleteProductImage($filename) { $this->deleteImage($filename); }
}

?>