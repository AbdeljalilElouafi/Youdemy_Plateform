<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\VideoCourse;

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header('Location: /login.php');
    exit;
}

$courseModel = new VideoCourse();
$students = $courseModel->getTeacherStudents($_SESSION['user']['id']);

require_once __DIR__ . '/../View/teacher/total-students.php';
?>