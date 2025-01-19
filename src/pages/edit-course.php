<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\VideoCourse;
use App\Model\TextCourse;

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header('Location: /login.php');
    exit;
}

$error = '';

// I get the course by slug
$slug = isset($_GET['slug']) ? $_GET['slug'] : null;
$courseModel = new VideoCourse(); 
$categoryModel = new \App\Model\CategoryModel();
$tagModel = new \App\Model\TagModel();

$categories = $categoryModel->getAllCategories();
$tags = $tagModel->getAllTags();

if ($slug) {
    $course = $courseModel->getCourseBySlug($slug);
    
    if (!$course) {
        $error = "Course not found.";
    }
} else {
    $error = "Invalid course data.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $courseData = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'category_id' => $_POST['category'],
            'status' => $_POST['status'],
            'teacher_id' => $_SESSION['user']['id']
        ];

        $tagIds = $_POST['tags'] ?? [];
        $courseType = $_POST['content_type'];

        if ($courseType === 'video') {
            $courseData['video_url'] = $_POST['video_url'];
            $courseModel = new VideoCourse();
        } else {
            $courseData['content'] = $_POST['content'];
            $courseModel = new TextCourse();
        }

        $courseId = $courseModel->updateVCourse($course['id'], $courseData); 

        if ($courseId) {
            $courseModel->createCourseTags($courseId, $tagIds);  
            $_SESSION['success'] = 'Course edited successfully!';
            header('Location: teacher-page.php');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once __DIR__ . '/../View/teacher/edit-course.php';
?>
