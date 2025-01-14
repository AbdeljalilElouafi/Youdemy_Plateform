<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation bar remains the same -->
    
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Add New Course</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="title" name="title" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Content Type</label>
                        <div class="mt-2 space-y-4">
                            <div>
                                <input type="radio" id="type_video" name="content_type" value="video" checked
                                       onchange="toggleContentType()"
                                       class="mr-2">
                                <label for="type_video">Video</label>
                            </div>
                            <div>
                                <input type="radio" id="type_document" name="content_type" value="document"
                                       onchange="toggleContentType()"
                                       class="mr-2">
                                <label for="type_document">Document</label>
                            </div>
                        </div>
                    </div>

                    <div id="video_content" class="space-y-4">
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL</label>
                            <input type="url" id="video_url" name="video_url"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div>
                            <label for="video_duration" class="block text-sm font-medium text-gray-700">Duration (in seconds)</label>
                            <input type="number" id="video_duration" name="video_duration"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>

                    <div id="document_content" class="hidden">
                        <label for="document_file" class="block text-sm font-medium text-gray-700">Upload Document (PDF or Word)</label>
                        <input type="file" id="document_file" name="document_file" accept=".pdf,.doc,.docx"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category" name="category" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <div class="grid grid-cols-3 gap-4">
                            <?php foreach ($tags as $tag): ?>
                                <div class="flex items-center">
                                    <input type="checkbox" id="tag_<?= $tag['id'] ?>" name="tags[]" 
                                           value="<?= htmlspecialchars($tag['id']) ?>"
                                           class="mr-2">
                                    <label for="tag_<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="../../pages/teacher-page.php" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Create Course
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleContentType() {
            const videoContent = document.getElementById('video_content');
            const documentContent = document.getElementById('document_content');
            const isVideo = document.getElementById('type_video').checked;

            videoContent.classList.toggle('hidden', !isVideo);
            documentContent.classList.toggle('hidden', isVideo);

            // Reset the fields when switching
            if (isVideo) {
                document.getElementById('document_file').value = '';
            } else {
                document.getElementById('video_url').value = '';
                document.getElementById('video_duration').value = '';
            }
        }
    </script>
</body>
</html>