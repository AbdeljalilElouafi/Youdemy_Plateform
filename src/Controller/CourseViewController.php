<?php
namespace App\Controller;

use App\Model\VideoCourse;

class CourseViewController
{
    private VideoCourse $courseModel;
    private ?array $course = null;
    private bool $isEnrolled = false;
    
    public function __construct()
    {
        session_start();
        $this->courseModel = new VideoCourse();
    }
    
    public function handleRequest(): void
    {
        $this->checkAuth();
        $this->loadCourse();
        $this->checkEnrollmentStatus();
        $this->displayPage();
    }
    
    private function checkAuth(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login.php');
            exit();
        }
    }
    
    private function loadCourse(): void
    {
        $courseId = $this->getCourseId();
        
        if (!$courseId) {
            header('Location: ../pages/student-page.php');
            exit();
        }
        
        $this->course = $this->courseModel->getCourseWithContent($courseId);
        
        if (!$this->course) {
            header('Location: ../View/student/student-page.php');
            exit();
        }
    }
    
    private function getCourseId(): int
    {
        return isset($_GET['id']) ? (int)$_GET['id'] : 0;
    }
    
    private function checkEnrollmentStatus(): void
    {
        if (isset($_SESSION['user']['id'])) {
            $enrolledCourses = $this->courseModel->getStudentCourses($_SESSION['user']['id']);
            $courseId = $this->getCourseId();
            
            $this->isEnrolled = !empty(array_filter($enrolledCourses, function($course) use ($courseId) {
                return $course['id'] == $courseId;
            }));
        }
    }
    
    private function displayPage(): void
    {
        $viewData = [
            'course' => $this->course,
            'isEnrolled' => $this->isEnrolled
        ];
        
        extract($viewData);
        require_once __DIR__ . '/../View/student/course-page.php';
    }
    

    public function getCourse(): ?array
    {
        return $this->course;
    }
    
    public function getIsEnrolled(): bool
    {
        return $this->isEnrolled;
    }
}