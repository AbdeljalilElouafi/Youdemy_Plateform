<?php
namespace App\Controller;

use App\Model\CategoryModel;
use App\Model\TagModel;
use App\Model\CourseModel;
use App\Model\VideoCourse;
use Exception;

class EditCourseController
{
    private CategoryModel $categoryModel;
    private TagModel $tagModel;
    private CourseModel $courseModel;
    private string $error = '';
    private ?array $course = null;
    private ?string $slug = null;
    
    public function __construct()
    {
        session_start();
        $this->categoryModel = new CategoryModel();
        $this->tagModel = new TagModel();
        $this->courseModel = new VideoCourse();
        $this->slug = $_GET['slug'] ?? null;
    }
    
    public function checkAuth(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
            header('Location: /login.php');
            exit;
        }
    }
    
    public function handleRequest(): void
    {
        $this->checkAuth();
        
        if (!$this->loadCourse()) {
            $this->error = "Course not found or invalid slug.";
            $this->displayPage();
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        }
        
        $this->displayPage();
    }
    
    private function loadCourse(): bool
    {
        if (!$this->slug) {
            return false;
        }
        
        $this->course = $this->courseModel->getCourseWithTags($this->slug);
        return $this->course !== null;
    }
    
    private function handlePostRequest(): void
    {
        try {
            $courseData = $this->prepareCourseData();
            $tagIds = $_POST['tags'] ?? [];
            
            // Start transaction
            $this->courseModel->getDB()->beginTransaction();
            
            $success = $this->courseModel->updateCourse($this->course['id'], $courseData);
            
            if ($success) {
                // Update course tags
                $this->courseModel->deleteCourseTags($this->course['id']);
                if (!empty($tagIds)) {
                    $this->courseModel->createCourseTags($this->course['id'], $tagIds);
                }
                
                $this->courseModel->getDB()->commit();
                $_SESSION['success'] = 'Course updated successfully!';
                header('Location: teacher-page.php');
                exit;
            } else {
                $this->courseModel->getDB()->rollBack();
                $this->error = "Failed to update course";
            }
            
        } catch (Exception $e) {
            $this->courseModel->getDB()->rollBack();
            $this->error = $e->getMessage();
        }
    }
    
    private function prepareCourseData(): array
    {
        $contentType = $_POST['content_type'];
        
        $courseData = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'category_id' => $_POST['category'],
            'status' => $_POST['status'],
            'content_type' => $contentType,
            'content_url' => null,
            'content_text' => null
        ];

        if ($contentType === 'video') {
            $courseData['content_url'] = $_POST['video_url'];
        } else {
            $courseData['content_text'] = $_POST['content'];
        }
        
        return $courseData;
    }
    
    private function displayPage(): void
    {
        $viewData = [
            'categories' => $this->categoryModel->getAllCategories(),
            'tags' => $this->tagModel->getAllTags(),
            'course' => $this->course,
            'error' => $this->error,
            'slug' => $this->slug
        ];
        
        extract($viewData);
        require_once __DIR__ . '/../View/teacher/edit-course.php';
    }
    
    public function getError(): string
    {
        return $this->error;
    }
}