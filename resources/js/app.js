import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Comments load more functionality
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreButton = document.getElementById('load-more-comments');
    
    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const currentOffset = parseInt(this.dataset.offset);
            const commentsContainer = document.getElementById('comments-list');
            
            // Show loading state
            this.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Ładowanie...
            `;
            this.disabled = true;
            
            // Fetch more comments
            fetch(`/posts/${postId}/comments/load-more?offset=${currentOffset}`)
                .then(response => response.json())
                .then(data => {
                    // Add new comments to the list
                    data.comments.forEach(comment => {
                        const commentHtml = `
                            <div class="flex gap-4 mb-6">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        ${comment.author_name.substring(0, 2).toUpperCase()}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-gray-900">${comment.author_name}</h4>
                                                ${comment.is_from_logged_user ? '<span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Użytkownik</span>' : ''}
                                            </div>
                                            <span class="text-sm text-gray-500">${comment.created_at}</span>
                                        </div>
                                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">${comment.content}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        commentsContainer.insertAdjacentHTML('beforeend', commentHtml);
                    });
                    
                    // Update button state
                    if (data.hasMore) {
                        this.dataset.offset = currentOffset + data.comments.length;
                        this.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            Pokaż więcej komentarzy
                        `;
                        this.disabled = false;
                    } else {
                        this.remove(); // Remove button if no more comments
                    }
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    this.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        Błąd - spróbuj ponownie
                    `;
                    this.disabled = false;
                });
        });
    }
});
