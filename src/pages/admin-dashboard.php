<?php

require_once '../../vendor/autoload.php';


$userModel = new App\Model\UserModel();
$courseModel = new App\Model\TextCourse();
$categoryModel = new App\Model\CategoryModel();
$tagModel = new App\Model\TagModel();


$teacherCount = $userModel->countTeachers();
$studentCount = $userModel->countStudents();
$categoryCount = $categoryModel->countCategories();
$tagCount = count($tagModel->getAllTags());
$topTeachers = $userModel->getTopTeachers();
$topCourses = $courseModel->getTopCourses();

require_once __DIR__ . '/../../public/index.php';

?>