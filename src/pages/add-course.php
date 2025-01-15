<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header('Location: /login.php');
    exit;
}

$error = '';



$categoryModel = new \App\Model\CategoryModel();
$tagModel = new \App\Model\TagModel();

$categories = $categoryModel->getAllCategories();
$tags = $tagModel->getAllTags();


try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $courseData = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'teacher_id' => $_SESSION['user']['id'],
            'category_id' => $_POST['category'],
            'status' => 'draft'
        ];


        $tagIds = $_POST['tags'] ?? [];


        $courseType = $_POST['content_type']; 
        
        if ($courseType === 'video') {
            $courseData['video_url'] = $_POST['video_url'];
        } else {
            $courseData['content'] = $_POST['content'];
        }


        $course = \App\Model\CourseFactory::createCourse($courseType);
        

        $courseId = $course->addContent($courseData, $tagIds);

        if ($courseId) {
            $_SESSION['success'] = 'Course created successfully!';
            header('Location: teacher-page.php');
            exit;
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}


require_once __DIR__ . '/../View/teacher/add-course.php';