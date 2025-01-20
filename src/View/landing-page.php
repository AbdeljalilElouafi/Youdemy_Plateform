<?php

    require_once '../../vendor/autoload.php';
    use App\Model\CourseModel;

    $courseModel = new \App\Model\VideoCourse();
    // $courseModel = new CourseModel();
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 6; 
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $courses = $courseModel->getCourses($page, $limit, $search);
    $totalCourses = $courseModel->getTotalCourses($search);
    $totalPages = ceil($totalCourses / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youdemy - Online Learning Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-300">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="landing-page.php" class="text-2xl font-bold text-indigo-600">Youdemy</a>
                </div>
                <div class="flex items-center">
                    <a href="../pages/login.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Login</a>
                </div>
            </div>
        </div>
    </nav>
 
    <!-- Hero Section -->
    <div class="bg-slate-700 text-white">
        <div class="max-w-7xl mx-auto px-4 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Welcome to Youdemy</h1>
                <p class="text-xl mb-8">Discover the best online courses and start learning today</p>
                
                <!-- Search Form -->
                <form action="" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex gap-2">
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                               class="w-full px-4 py-2 rounded-md text-gray-900" 
                               placeholder="Search courses...">

                               <div>
                    <select name="category" class="w-full bg-gray-600 px-4 py-2 rounded-md">
                        <option value="">Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"
                                    <?= ($selectedCategory ?? '') == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                        <button type="submit" class="bg-indigo-500 px-6 py-2 rounded-md hover:bg-indigo-600">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
 
    <!-- Course Catalog -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold mb-8">Available Courses</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($courses as $course): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($course['title']) ?></h3>
                    <p class="text-gray-600 mb-4"><?= htmlspecialchars($course['description']) ?></p>
                    <div class="flex justify-between items-center">
                        
                        <a href="../pages/login.php" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center mt-8 gap-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="/Youdemy_Plateform/src/view/landing-page.php?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                  class="<?= $page === $i ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-600' ?> 
                         px-4 py-2 rounded-md hover:bg-indigo-700 hover:text-white">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-700 text-white py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">About Youdemy</h3>
                    <p>Your gateway to online learning excellence, Created by: AbdeljalilElouafi</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul>
                        <li><a href="/about" class="hover:text-indigo-400">About Us</a></li>
                        <li><a href="/contact" class="hover:text-indigo-400">Contact</a></li>
                        <li><a href="/terms" class="hover:text-indigo-400">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <p>Email: abdeljalileloufi2@gmail.com</p>
                    <p>Phone: 0666666666</p>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p>&copy; <?= date('Y') ?> Youdemy. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>