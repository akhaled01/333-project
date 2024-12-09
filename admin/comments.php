<?php
session_start();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-zinc-100 mb-6">Comments Management</h1>
    
    <div id="comments-list" class="space-y-4">
        <!-- Comments will be loaded here -->
    </div>
</div>

<!-- Reply Dialog -->
<dialog id="reply-dialog" class="bg-zinc-800 rounded-lg shadow-xl p-0 backdrop:bg-zinc-900/90 border border-zinc-700">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-zinc-100 mb-4">Reply to Comment</h3>
        <form id="reply-form" method="dialog" class="space-y-4">
            <input type="hidden" id="comment-id" name="comment-id">
            <div>
                <label for="reply-text" class="block text-sm font-medium text-zinc-300 mb-2">Your Response</label>
                <textarea
                    id="reply-text"
                    name="reply-text"
                    rows="4"
                    class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-zinc-100"
                    placeholder="Type your reply here..."
                ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button
                    type="button"
                    class="px-4 py-2 text-sm font-medium text-zinc-300 hover:text-zinc-100 focus:outline-none"
                    onclick="this.closest('dialog').close()"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Submit Reply
                </button>
            </div>
        </form>
    </div>
</dialog>

<script src="./js/comments.js"></script>
