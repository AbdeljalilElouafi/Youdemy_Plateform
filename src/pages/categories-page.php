<?php
require_once '../../vendor/autoload.php';
use App\Model\CategoryModel;

$category = new CategoryModel();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description']
                ];
                if ($category->addCategory($data)) {
                    $message = "Category added successfully!";
                } else {
                    $error = "Error adding category.";
                }
                break;

            case 'edit':
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description']
                ];
                if ($category->editCategory($_POST['id'], $data)) {
                    $message = "Category updated successfully!";
                } else {
                    $error = "Error updating category.";
                }
                break;

            case 'delete':
                if ($category->deleteCategory($_POST['id'])) {
                    $message = "Category deleted successfully!";
                } else {
                    $error = "Error deleting category.";
                }
                break;
        }
    }
}

$categories = $category->getCategoriesWithCounts();

require_once __DIR__ . '/../View/admin/categories-view.php';

?>
