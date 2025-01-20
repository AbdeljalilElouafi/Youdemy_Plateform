<?php
namespace App\Controller;

use App\Model\VideoCourse;
use App\Model\CategoryModel;
use Exception;

class AdminCoursesController 
{
   private VideoCourse $courseModel;
   private CategoryModel $categoryModel;
   private string $message = '';
   private string $error = '';
   private int $limit = 30;
   
   public function __construct()
   {
       session_start();
       $this->courseModel = new VideoCourse();
       $this->categoryModel = new CategoryModel();
   }
   
   public function handleRequest(): void
   {
       $this->checkAuth();
       
       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           $this->handlePostRequest();
       }
       
       $this->displayPage();
   }
   
   private function checkAuth(): void
   {
       if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
           header('Location: /login.php');
           exit;
       }
   }
   
   private function handlePostRequest(): void
   {
       $action = $_POST['action'] ?? '';
       $courseId = $_POST['course_id'] ?? '';
       
       if (!empty($courseId)) {
           try {
               switch ($action) {
                   case 'publish':
                       $this->publishCourse($courseId);
                       break;
                       
                   case 'archive':
                       $this->archiveCourse($courseId);
                       break;
                       
                   case 'draft':
                       $this->draftCourse($courseId);
                       break;
                       
                   default:
                       $this->error = 'Invalid action.';
               }
           } catch (Exception $e) {
               $this->error = 'An error occurred: ' . $e->getMessage();
           }
       }
   }
   
   private function publishCourse(string $courseId): void
   {
       $this->courseModel->updateCourse($courseId, [
           'status' => 'published',
           'published_at' => date('Y-m-d H:i:s')
       ]);
       $this->message = 'Course published successfully.';
   }
   
   private function archiveCourse(string $courseId): void
   {
       $this->courseModel->updateCourse($courseId, [
           'status' => 'archived'
       ]);
       $this->message = 'Course archived successfully.';
   }
   
   private function draftCourse(string $courseId): void
   {
       $this->courseModel->updateCourse($courseId, [
           'status' => 'draft',
           'published_at' => null
       ]);
       $this->message = 'Course moved to draft successfully.';
   }
   
   private function displayPage(): void
   {
       try {
           $viewData = [
               'courses' => $this->getCourses(),
               'categories' => $this->categoryModel->getAllCategories(),
               'message' => $this->message,
               'error' => $this->error,
               'currentFilters' => $this->getCurrentFilters()
           ];
           
           extract($viewData);
           require_once __DIR__ . '/../View/admin/courses-view.php';
           
       } catch (Exception $e) {
           $this->error = 'Error fetching courses: ' . $e->getMessage();
           extract([
               'courses' => [],
               'categories' => [],
               'message' => $this->message,
               'error' => $this->error,
               'currentFilters' => $this->getCurrentFilters()
           ]);
           require_once __DIR__ . '/../View/admin/courses-view.php';
       }
   }
   
   private function getCourses(): array
   {
       $filters = $this->getCurrentFilters();
       
       return $this->courseModel->searchAllCourses(
           $filters['search'],
           $filters['page'],
           $this->limit,
           $filters['categoryId'],
           $filters['statusFilter']
       );
   }
   
   private function getCurrentFilters(): array
   {
       return [
           'statusFilter' => $_GET['status'] ?? '',
           'categoryId' => $_GET['category'] ?? '',
           'search' => $_GET['search'] ?? '',
           'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1
       ];
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