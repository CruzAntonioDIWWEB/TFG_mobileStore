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
        
        // Filter by brand if specified in URL
        $selectedBrand = $this->getGetData('brand');
        $filteredPhones = $phones;
        
        if ($selectedBrand && $selectedBrand !== 'all') {
            require_once __DIR__ . '/../Helpers/BrandHelper.php';
            $filteredPhones = array_filter($phones, function($phone) use ($selectedBrand) {
                $detectedBrand = \Helpers\BrandHelper::detectBrand($phone['name']);
                return $detectedBrand === $selectedBrand;
            });
        }
        
        $viewData = [
            'phones' => $filteredPhones,
            'allPhones' => $phones, // For brand extraction
            'categoryName' => 'Móviles',
            'selectedBrand' => $selectedBrand
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
     * Display all products in admin panel
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
        $price = floatval($postData['price'] ?? 0);
        $stock = intval($postData['stock'] ?? 0);
        $categoryId = intval($postData['category_id'] ?? 0);
        $accessoryTypeId = !empty($postData['accessory_type_id']) ? intval($postData['accessory_type_id']) : null;
        $mobileBrand = trim($postData['mobile_brand'] ?? '');

        // Validation
        if (empty($name)) {
            $this->setErrorMessage('El nombre del producto es obligatorio');
            $this->redirect('product', 'create');
            return;
        }

        if ($price <= 0 || $price > 999.99) {
            $this->setErrorMessage('El precio debe estar entre 0.01 € y 999.99 €');
            $this->redirect('product', 'create');
            return;
        }

        if ($stock < 0) {
            $this->setErrorMessage('El stock no puede ser negativo');
            $this->redirect('product', 'create');
            return;
        }

        if ($categoryId <= 0) {
            $this->setErrorMessage('Debe seleccionar una categoría válida');
            $this->redirect('product', 'create');
            return;
        }

        // Check if it's a mobile category and validate brand
        $categoryModel = new \Models\Category();
        $category = $categoryModel->getById($categoryId);
        $isMobileCategory = $category && (stripos($category->getName(), 'móvil') !== false || 
                                        stripos($category->getName(), 'teléfono') !== false);

        if ($isMobileCategory && empty($mobileBrand)) {
            $this->setErrorMessage('Debe seleccionar una marca para productos móviles');
            $this->redirect('product', 'create');
            return;
        }

        // For mobile products, validate that the name matches the selected brand
        if ($isMobileCategory && !empty($mobileBrand)) {
            require_once __DIR__ . '/../Helpers/BrandHelper.php';
            $detectedBrand = \Helpers\BrandHelper::detectBrand($name);
            
            if ($mobileBrand !== 'other' && $detectedBrand !== $mobileBrand) {
                // Log the discrepancy but don't block - admin might know better
                error_log("Brand mismatch for product '$name': selected '$mobileBrand', detected '$detectedBrand'");
            }
        }

        // Handle image upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setCategoryId($categoryId);
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

        if ($productId <= 0) {
            $this->setErrorMessage('ID de producto inválido');
            $this->redirect('product', 'index');
            return;
        }

        // Get existing product
        $productModel = new \Models\Product();
        $existingProductResult = $productModel->getProductById($productId);

        if (!$existingProductResult) {
            $this->setErrorMessage('Producto no encontrado');
            $this->redirect('product', 'index');
            return;
        }

        // Validate input data
        $name = trim($postData['name'] ?? '');
        $description = trim($postData['description'] ?? '');
        $price = floatval($postData['price'] ?? 0);
        $stock = intval($postData['stock'] ?? 0);
        $categoryId = intval($postData['category_id'] ?? 0);
        $accessoryTypeId = !empty($postData['accessory_type_id']) ? intval($postData['accessory_type_id']) : null;
        $mobileBrand = trim($postData['mobile_brand'] ?? '');

        // Validation
        if (empty($name)) {
            $this->setErrorMessage('El nombre del producto es obligatorio');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        if ($price <= 0 || $price > 999.99) {
            $this->setErrorMessage('El precio debe estar entre 0.01 € y 999.99 €');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        if ($stock < 0) {
            $this->setErrorMessage('El stock no puede ser negativo');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        if ($categoryId <= 0) {
            $this->setErrorMessage('Debe seleccionar una categoría válida');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        // Check if it's a mobile category and validate brand
        $categoryModel = new \Models\Category();
        $category = $categoryModel->getById($categoryId);
        $isMobileCategory = $category && (stripos($category->getName(), 'móvil') !== false || 
                                        stripos($category->getName(), 'teléfono') !== false);

        if ($isMobileCategory && empty($mobileBrand)) {
            $this->setErrorMessage('Debe seleccionar una marca para productos móviles');
            $this->redirect('product', 'edit', ['id' => $productId]);
            return;
        }

        // For mobile products, validate that the name matches the selected brand
        if ($isMobileCategory && !empty($mobileBrand)) {
            require_once __DIR__ . '/../Helpers/BrandHelper.php';
            $detectedBrand = \Helpers\BrandHelper::detectBrand($name);
            
            if ($mobileBrand !== 'other' && $detectedBrand !== $mobileBrand) {
                // Log the discrepancy but don't block - admin might know better
                error_log("Brand mismatch for product '$name': selected '$mobileBrand', detected '$detectedBrand'");
            }
        }

        // Handle the current image - work with both object and array returns
        $currentImage = null;
        if (is_object($existingProductResult)) {
            $currentImage = $existingProductResult->getImage();
        } elseif (is_array($existingProductResult)) {
            $currentImage = $existingProductResult['image'] ?? null;
        }

        // Handle image upload (only if new image provided)
        $imageName = $currentImage; // Keep existing image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setCategoryId($categoryId);
        $product->setAccessoryType($accessoryTypeId);
        $product->setImage($imageName);

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
     * Handle image upload for products 
     * @param array $file The uploaded file from $_FILES
     * @return string|false The filename on success, false on failure
     */
    private function handleImageUpload($file){
        // Validate file
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Check file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->setErrorMessage('La imagen es demasiado grande (máximo 5MB)');
            return false;
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            $this->setErrorMessage('Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP');
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '.' . strtolower($extension);
        
        // Upload directory
        $uploadDir = __DIR__ . '/../assets/img/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $filename;
        }
        
        $this->setErrorMessage('Error al subir la imagen');
        return false;
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