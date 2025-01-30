<!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title><?= htmlspecialchars($course['title']) ?> - Youdemy</title> <script src="https://cdn.tailwindcss.com"></script> <style> body { background: linear-gradient(135deg, #e0f7fa, #80deea); } .card { background: rgba(255, 255, 255, 0.95); border-radius: 30px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2); } .button { transition: transform 0.3s, background-color 0.3s; } .button:hover { transform: scale(1.1); background-color: #00796b; } .fade-in { animation: fadeIn 0.6s ease-in-out; } @keyframes fadeIn { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } } </style> </head> <body> <!-- Navigation --> <nav class="bg-white shadow-lg"> <div class="max-w-7xl mx-auto px-4"> <div class="flex justify-between h-16"> <div class="flex items-center"> <a href="student-page.php" class="text-3xl font-bold text-indigo-600">Youdemy</a> </div> <div class="flex items-center space-x-4"> <span class="text-gray-700">Welcome, <?= htmlspecialchars($_SESSION['user']['first_name']) ?></span> <a href="../pages/logout.php" class="text-red-600 hover:text-red-800">Logout</a> </div> </div> </div> </nav>


<div class="max-w-7xl mx-auto px-4 py-10">
    <!-- Course Header -->
    <div class="card p-8 mb-10 fade-in">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-5xl font-extrabold text-teal-700 mb-4"><?= htmlspecialchars($course['title']) ?></h1>
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
                        class="button bg-teal-600 text-white px-8 py-4 rounded-lg">
                    Enroll Now
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Course Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="card p-8 mb-10 fade-in">
                <h2 class="text-4xl font-bold text-teal-600 mb-4">Course Description</h2>
                <p class="text-gray-800 whitespace-pre-line"><?= htmlspecialchars($course['description']) ?></p>
            </div>

            <div class="card p-8 fade-in">
                <h2 class="text-4xl font-bold text-teal-600 mb-4">Course Content</h2>
                <?php if ($course['content_type'] === 'video' && $course['content_url']): ?>
                    <div class="aspect-w-16 aspect-h-9 mb-4">
                        <iframe width="560" height="315" src="<?= htmlspecialchars($course['content_url']) ?>"
                             title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                        </iframe>
                    </div>
                <?php elseif ($course['content_type'] === 'text' && $course['content_text']): ?> <div class="prose max-w-none"> 
                        <?= nl2br(htmlspecialchars($course['content_text'])) ?>
                    </div> 
                <?php else: ?>
                    <p class="text-gray-600">No content available yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="card p-8 sticky top-8 fade-in">
                <h2 class="text-3xl font-bold text-teal-600 mb-4">Course Information</h2>
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
</body> </html>