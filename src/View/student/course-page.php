
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['title']) ?> - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="student-page.php" class="text-2xl font-bold text-indigo-600">Youdemy</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['user']['first_name']) ?></span>
                    <a href="../pages/logout.php" class="text-red-600 hover:text-red-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Course Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($course['title']) ?></h1>
                    <div class="flex items-center space-x-4 text-gray-600 mb-4">
                        <span>Published: <?= date('M d, Y', strtotime($course['published_at'] ?? $course['created_at'])) ?></span>
                        <span>•</span>
                        <span>Status: <?= ucfirst(htmlspecialchars($course['status'])) ?></span>
                    </div>
                </div>
                <?php if (!$isEnrolled && $course['status'] === 'published'): ?>
                <form action="/student/enroll.php" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700">
                        Enroll Now
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Course Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-2xl font-bold mb-4">Course Description</h2>
                    <p class="text-gray-700 whitespace-pre-line"><?= htmlspecialchars($course['description']) ?></p>
                </div>


                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4">Course Content</h2>
                    <?php if ($course['content_type'] === 'video' && $course['content_url']): ?>
                        <div class="aspect-w-16 aspect-h-9 mb-4">
                            <iframe width="560" height="315" src="<?= htmlspecialchars($course['content_url']) ?>"
                                 title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                  referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                            </iframe>
                        </div>
                    <?php elseif ($course['content_type'] === 'text' && $course['content_text']): ?>
                        <div class="prose max-w-none">
                            <?= nl2br(htmlspecialchars($course['content_text'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-600">No content available yet.</p>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-xl font-bold mb-4">Course Information</h2>
                    <div class="space-y-4">
                        <?php if ($isEnrolled): ?>
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-md">
                                You are enrolled in this course
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Content Type:</span>
                            <span class="font-medium"><?= ucfirst(htmlspecialchars($course['content_type'] ?? 'Not specified')) ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-medium"><?= htmlspecialchars($course['category_id']) ?></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium"><?= date('M d, Y', strtotime($course['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>