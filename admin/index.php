<?php
require_once 'auth_middleware.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="/admin/js/index.js" type="module" defer></script>
    <script src="/admin/js/comments.js" defer></script>
    <script src="/admin/js/imageUpload.js" defer></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-zinc-900 antialiased text-zinc-100">
    <div class="flex min-h-screen">
        <div class="flex flex-1 flex-col">
            <?php include '../components/navbar.php'; ?>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-zinc-900 p-4 sm:p-6 lg:p-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-zinc-100 mb-8">Dashboard</h1>
                <div id="stats-container" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8"></div>
                <div id="room-management" class="rounded-lg bg-zinc-800 p-6 shadow-sm border border-zinc-700"></div>

                <!-- Comment Management Section -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-zinc-100 mb-4">Comment Management</h2>
                    <div id="comments-section" class="rounded-lg bg-zinc-800 p-6 shadow-sm border border-zinc-700">
                        <div id="comments-list" class="space-y-4">
                            <!-- Comments will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <custom-dialog id="add-room-dialog" title="Add New Room" description="Fill in the room details below.">
        <form id="add-room-form" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="room-name" class="block text-sm font-medium text-zinc-300">Room Name</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="room-name" required
                            class="block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2"
                            placeholder="Enter room name">
                    </div>
                </div>

                <div>
                    <label for="room-capacity" class="block text-sm font-medium text-zinc-300">Capacity</label>
                    <div class="mt-1">
                        <input type="number" name="capacity" id="room-capacity" required min="1"
                            class="block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2"
                            placeholder="Enter room capacity">
                    </div>
                </div>
            </div>

            <div>
                <label for="room-equipment" class="block text-sm font-medium text-zinc-300">Equipment</label>
                <div class="mt-1">
                    <textarea name="equipment" id="room-equipment" rows="2" required
                        class="block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2"
                        placeholder="List room equipment..."></textarea>
                </div>
            </div>

            <div>
                <label for="room-image" class="block text-sm font-medium text-zinc-300">Room Image</label>
                <div class="mt-1 flex justify-center rounded-lg border border-dashed border-zinc-600 px-4 py-4 hover:border-zinc-400 transition-colors">
                    <div class="text-center">
                        <div id="image-preview" class="hidden mb-3">
                            <img src="" alt="Preview" class="mx-auto h-24 w-auto rounded-lg object-cover">
                        </div>
                        <div class="space-y-1">
                            <svg class="mx-auto h-8 w-8 text-zinc-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex justify-center text-sm">
                                <label for="room-image"
                                    class="relative cursor-pointer rounded-md bg-zinc-700 px-3 py-2 text-sm font-medium text-zinc-300 hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-800">
                                    <span>Upload Image</span>
                                    <input id="room-image" name="image" type="file" accept="image/*" class="sr-only" onchange="previewImage(this)">
                                </label>
                            </div>
                            <p class="text-xs text-zinc-400">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" slot="confirm"
                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-800">
                    Add Room
                </button>
            </div>
        </form>
    </custom-dialog>

    <!-- Comment Reply Dialog -->
    <custom-dialog id="reply-comment-dialog" title="Reply to Comment" description="Add your reply to this comment.">
        <form id="reply-comment-form" class="space-y-6">
            <input type="hidden" id="comment-id" name="comment_id">
            <div>
                <label for="reply-text" class="block text-sm font-medium text-zinc-300">Your Reply</label>
                <div class="mt-1">
                    <textarea name="reply" id="reply-text" rows="4" required
                        class="block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3"
                        placeholder="Enter your reply..."></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" slot="confirm"
                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-800">
                    Submit Reply
                </button>
            </div>
        </form>
    </custom-dialog>
</body>

</html>