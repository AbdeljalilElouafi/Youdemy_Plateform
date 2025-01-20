<?php
namespace App\Controller;

use App\Model\CategoryModel;
use App\Model\TagModel;
use App\Model\CourseFactory;
use Exception;

class AddCourseController
{
    private CategoryModel $categoryModel;
    private TagModel $tagModel;
    private string $error = '';
    
    public function __construct()
    {
        session_start();
        $this->categoryModel = new CategoryModel();
        $this->tagModel = new TagModel();
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
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        }
        
        $this->displayPage();
    }
    
    private function handlePostRequest(): void
    {
        try {
            $courseData = $this->prepareCourseData();
            $tagIds = $_POST['tags'] ?? [];
            $courseType = $_POST['content_type'];
            
            if ($courseType === 'video') {
                $courseData['video_url'] = $_POST['video_url'];
            } else {
                $courseData['content'] = $_POST['content'];
            }
            
            $course = CourseFactory::createCourse($courseType);
            $courseId = $course->addContent($courseData, $tagIds);
            
            if ($courseId) {
                $_SESSION['success'] = 'Course created successfully!';
                header('Location: teacher-page.php');
                exit;
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
    }
    
    private function prepareCourseData(): array
    {
        return [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'teacher_id' => $_SESSION['user']['id'],
            'category_id' => $_POST['category'],
            'status' => 'draft'
        ];
    }
    
    private function displayPage(): void
    {
        $viewData = [
            'categories' => $this->categoryModel->getAllCategories(),
            'tags' => $this->tagModel->getAllTags(),
            'error' => $this->error
        ];
        
        extract($viewData);
        require_once __DIR__ . '/../View/teacher/add-course.php';
    }
    
    public function getError(): string
    {
        return $this->error;
    }
}