<?php
require_once '../../vendor/autoload.php';
use App\Model\TagModel;

$tag = new TagModel();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':

                $tagNames = explode(',', $_POST['names']);
                $success = true;

                foreach ($tagNames as $tagName) {
                    $tagName = trim($tagName);
                    if (!empty($tagName)) {

                        $data = ['name' => $tagName];

                        if (!$tag->addTag($data)) {
                            $success = false;

                            break;
                        }
                    }
                }

                
                if ($success) {
                    $message = "Tags added successfully!";
                } else {
                    $error = "Error adding tags.";
                }
                break;

            case 'edit':
                $data = ['name' => $_POST['name']];
                if ($tag->editTag($_POST['id'], $data)) {
                    $message = "Tag updated successfully!";
                } else {
                    $error = "Error updating tag.";
                }
                break;

            case 'delete':
                if ($tag->deleteTag($_POST['id'])) {
                    $message = "Tag deleted successfully!";
                } else {
                    $error = "Error deleting tag.";
                }
                break;
        }
    }
}

$tags = $tag->getAllTags();

require_once __DIR__ . '/../View/admin/tags-view.php';


?>