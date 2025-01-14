<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header('Location: /login.php');
    exit;
}

$error = '';
$success = '';


$courseModel = new \App\Model\CourseModel();
$categoryModel = new \App\Model\CategoryModel();
$tagModel = new \App\Model\TagModel();


$categories = $categoryModel->getAllCategories();
$tags = $tagModel->getAllTags();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle file upload for document type
        $contentUrl = '';
        $contentType = $_POST['content_type'];
        
        if ($contentType === 'document') {
            if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!in_array($_FILES['document_file']['type'], $allowedTypes)) {
                    throw new Exception('Invalid file type. Only PDF and Word documents are allowed.');
                }
                
                $uploadDir = __DIR__ . '/../../public/uploads/documents/';
                $fileName = uniqid() . '_' . basename($_FILES['document_file']['name']);
                $contentUrl = '/uploads/documents/' . $fileName;
                
                if (!move_uploaded_file($_FILES['document_file']['file'], $uploadDir . $fileName)) {
                    throw new Exception('Failed to upload file.');
                }
            }
        } else {
            // For video type, just store the URL
            $contentUrl = $_POST['video_url'];
        }

        // Prepare course data
        $courseData = [
            'teacher_id' => $_SESSION['user']['id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'category_id' => $_POST['category'],
            'status' => $_POST['status'],
            'content' => [
                'type' => $contentType,
                'url' => $contentUrl,
                'duration' => $contentType === 'video' ? $_POST['video_duration'] : null,
                'file_size' => $contentType === 'document' ? $_FILES['document_file']['size'] : null
            ]
        ];

        // Get selected tags
        $selectedTags = isset($_POST['tags']) ? $_POST['tags'] : [];

        if ($courseModel->createCourseWithContent($courseData, $selectedTags)) {
            header('Location: teacher-page.php');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once __DIR__ . '/../View/teacher/add-course.php';
?>