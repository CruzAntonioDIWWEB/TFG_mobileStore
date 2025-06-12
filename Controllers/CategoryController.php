<?php

namespace Controllers;

require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/BaseController.php';

/**
 * CategoryController
 * Handles category CRUD operations for admin users
 */
class CategoryController extends BaseController
{
    
    // ========================================
    // CATEGORY DISPLAY METHODS
    // ========================================

    /**
     * Display all categories (admin only)
     */
    public function index(){
        $this->requireAdmin();

        $categoryModel = new \Models\Category();
        $categories = $categoryModel->getAll();

        $this->loadView('admin/categories/index', ['categories' => $categories]);
    }

    /**
     * Show form to create a new category
     */
    public function create(){
        $this->requireAdmin();

        $this->loadView('admin/categories/create');
    }

    /**
     * Show form to edit an existing category
     */
    public function edit(){
        $this->requireAdmin();

        $categoryId = $this->getGetData('id');
        
        if (!$categoryId || !is_numeric($categoryId)) {
            $this->setErrorMessage('ID de categoría inválido');
            $this->redirect('category', 'index');
            return;
        }

        $categoryModel = new \Models\Category();
        $category = $categoryModel->getById($categoryId);

        if (!$category) {
            $this->setErrorMessage('Categoría no encontrada');
            $this->redirect('category', 'index');
            return;
        }

        $this->loadView('admin/categories/edit', ['category' => $category]);
    }

    // ========================================
    // CATEGORY CRUD OPERATIONS
    // ========================================

    /**
     * Save a new category
     */
    public function save(){
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('category', 'index');
            return;
        }

        $postData = $this->getPostData();
        $name = trim($postData['name'] ?? '');

        // Validate required fields
        if (empty($name)) {
            $this->setErrorMessage('El nombre de la categoría es obligatorio');
            $this->redirect('category', 'create');
            return;
        }

        // Check if category name already exists
        $categoryModel = new \Models\Category();
        $existingCategory = $categoryModel->getByName($name);

        if ($existingCategory) {
            $this->setErrorMessage('Ya existe una categoría con ese nombre');
            $this->redirect('category', 'create');
            return;
        }

        // Create and save new category
        $categoryModel->setName($name);
        $saved = $categoryModel->saveDB();

        if ($saved) {
            $this->setSuccessMessage('Categoría creada exitosamente');
        } else {
            $this->setErrorMessage('Error al crear la categoría');
        }

        $this->redirect('category', 'index');
    }

    /**
     * Update an existing category
     */
    public function update(){
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('category', 'index');
            return;
        }

        $postData = $this->getPostData();
        $categoryId = intval($postData['id'] ?? 0);
        $name = trim($postData['name'] ?? '');

        // Validate required fields
        if (!$categoryId || empty($name)) {
            $this->setErrorMessage('ID de categoría y nombre son obligatorios');
            $this->redirect('category', 'index');
            return;
        }

        // Get category to update
        $categoryModel = new \Models\Category();
        $category = $categoryModel->getById($categoryId);

        if (!$category) {
            $this->setErrorMessage('Categoría no encontrada');
            $this->redirect('category', 'index');
            return;
        }

        // Check if name already exists (excluding current category)
        $existingCategory = $categoryModel->getByName($name);
        if ($existingCategory && $existingCategory->getId() != $categoryId) {
            $this->setErrorMessage('Ya existe una categoría con ese nombre');
            $this->redirect('category', 'edit', ['id' => $categoryId]);
            return;
        }

        // Update category
        $category->setName($name);
        $updated = $category->updateDB();

        if ($updated) {
            $this->setSuccessMessage('Categoría actualizada exitosamente');
        } else {
            $this->setErrorMessage('Error al actualizar la categoría');
        }

        $this->redirect('category', 'index');
    }

    /**
     * Delete a category
     */
    public function delete(){
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->setErrorMessage('Método de petición inválido');
            $this->redirect('category', 'index');
            return;
        }

        $categoryId = intval($this->getPostData('id') ?? 0);

        if (!$categoryId) {
            $this->setErrorMessage('ID de categoría inválido');
            $this->redirect('category', 'index');
            return;
        }

        // Get category to delete
        $categoryModel = new \Models\Category();
        $category = $categoryModel->getById($categoryId);

        if (!$category) {
            $this->setErrorMessage('Categoría no encontrada');
            $this->redirect('category', 'index');
            return;
        }

        // Check if category has products before deleting
        // Note: You might want to add a method to check if category has products
        // For now, we'll just attempt to delete

        $category->setId($categoryId);
        $deleted = $category->delete();

        if ($deleted) {
            $this->setSuccessMessage('Categoría eliminada exitosamente');
        } else {
            $this->setErrorMessage('Error al eliminar la categoría. Puede que tenga productos asociados.');
        }

        $this->redirect('category', 'index');
    }
}