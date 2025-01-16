<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Edit Course</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="edit-course.php?slug=<?= htmlspecialchars($slug) ?>" method="POST" enctype="multipart/form-data">
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="title" name="title" required
                               value="<?= htmlspecialchars($course['title']) ?>"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"><?= htmlspecialchars($course['description']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Content Type</label>
                        <div class="mt-2 space-y-4">
                            <div>
                                <input type="radio" id="type_video" name="content_type" value="video"
                                       <?= (!empty($course['video_url'])) ? 'checked' : '' ?>
                                       onchange="toggleContentType()"
                                       class="mr-2">
                                <label for="type_video">Video</label>
                            </div>
                            <div>
                                <input type="radio" id="type_document" name="content_type" value="text"
                                       <?= (!empty($course['content'])) ? 'checked' : '' ?>
                                       onchange="toggleContentType()"
                                       class="mr-2">
                                <label for="type_document">Text</label>
                            </div>
                        </div>
                    </div>

                    <div id="video_content" class="space-y-4 <?= empty($course['video_url']) ? 'hidden' : '' ?>">
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL</label>
                            <input type="url" id="video_url" name="video_url"
                                   value="<?= htmlspecialchars($course['video_url'] ?? '') ?>"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>

                    <div id="document_content" class="<?= empty($course['content']) ? 'hidden' : '' ?>">
                        <label for="content" class="block text-sm font-medium text-gray-700">Write course content here: </label>
                        <textarea id="content" name="content" rows="4"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"><?= htmlspecialchars($course['content'] ?? '') ?></textarea>
                    </div>

                    <input type="hidden" name="status" value="<?= htmlspecialchars($course['status']) ?>">

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category" name="category" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>"
                                        <?= ($category['id'] == $course['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <div class="grid grid-cols-3 gap-4">
                            <?php 
                            $courseTags = array_column($course['tags'] ?? [], 'id');
                            foreach ($tags as $tag): 
                            ?>
                                <div class="flex items-center">
                                    <input type="checkbox" id="tag_<?= $tag['id'] ?>" name="tags[]" 
                                           value="<?= htmlspecialchars($tag['id']) ?>"
                                           <?= in_array($tag['id'], $courseTags) ? 'checked' : '' ?>
                                           class="mr-2">
                                    <label for="tag_<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="../pages/teacher-page.php" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Update Course
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


            if (isVideo) {
                document.getElementById('content').value = '';
            } else {
                document.getElementById('video_url').value = '';
            }
        }
    </script>
</body>
</html>