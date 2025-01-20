<?php
namespace App\Controller;

use App\Model\CategoryModel;
use Exception;

class CategoriesController
{
    private CategoryModel $categoryModel;
    private string $message = '';
    private string $error = '';
    
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }
    
    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        }
        
        $this->displayPage();
    }
    
    private function handlePostRequest(): void
    {
        $action = $_POST['action'] ?? '';
        
        try {
            switch ($action) {
                case 'add':
                    $this->handleAddCategory();
                    break;
                    
                case 'edit':
                    $this->handleEditCategory();
                    break;
                    
                case 'delete':
                    $this->handleDeleteCategory();
                    break;
                    
                default:
                    $this->error = "Invalid action.";
            }
        } catch (Exception $e) {
            $this->error = "An error occurred: " . $e->getMessage();
        }
    }
    
    private function handleAddCategory(): void
    {
        $data = $this->prepareCategoryData();
        
        if ($this->categoryModel->addCategory($data)) {
            $this->message = "Category added successfully!";
        } else {
            $this->error = "Error adding category.";
        }
    }
    
    private function handleEditCategory(): void
    {
        $data = $this->prepareCategoryData();
        $categoryId = $_POST['id'] ?? null;
        
        if ($categoryId && $this->categoryModel->editCategory($categoryId, $data)) {
            $this->message = "Category updated successfully!";
        } else {
            $this->error = "Error updating category.";
        }
    }
    
    private function handleDeleteCategory(): void
    {
        $categoryId = $_POST['id'] ?? null;
        
        if ($categoryId && $this->categoryModel->deleteCategory($categoryId)) {
            $this->message = "Category deleted successfully!";
        } else {
            $this->error = "Error deleting category.";
        }
    }
    
    private function prepareCategoryData(): array
    {
        return [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
    }
    
    private function displayPage(): void
    {
        $viewData = [
            'categories' => $this->categoryModel->getCategoriesWithCounts(),
            'message' => $this->message,
            'error' => $this->error
        ];
        
        extract($viewData);
        require_once __DIR__ . '/../View/admin/categories-view.php';
    }
    

    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function getError(): string
    {
        return $this->error;
    }
}