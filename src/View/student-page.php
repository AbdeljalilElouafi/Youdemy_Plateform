

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold text-indigo-600">Youdemy</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['user']['first_name']) ?></span>
                    <a href="../pages/logout.php" class="text-red-600 hover:text-red-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- My Courses Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6">My Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($enrolledCourses as $course): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($course['title']) ?></h3>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($course['description']) ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                Enrolled: <?= date('M d, Y', strtotime($course['enrolled_at'])) ?>
                            </span>
                            <a href="/student/course-view.php?id=<?= $course['id'] ?>" 
                               class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Continue Learning
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Available Courses Section -->
        <div>
            <h2 class="text-2xl font-bold mb-6">Available Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($availableCourses as $course): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($course['title']) ?></h3>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($course['description']) ?></p>
                        <form action="/student/enroll.php" method="POST">
                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                            <button type="submit" 
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Enroll Now
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>