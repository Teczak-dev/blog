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
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">${comment.author_name}</h4>
                                                ${comment.is_from_logged_user ? '<span class="px-2 py-1 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 text-xs rounded-full">Użytkownik</span>' : ''}
                                            </div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">${comment.created_at}</span>
                                        </div>
                                        <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">${comment.content}</div>
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

    // AJAX comment submission to avoid page refresh
    const commentForms = document.querySelectorAll('form[action*="/comments"]');
    
    commentForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonContent = submitButton.innerHTML;
            
            // Show loading state
            submitButton.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Wysyłanie...
            `;
            submitButton.disabled = true;
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Submit comment via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (response.redirected) {
                    // If response is a redirect, handle success
                    window.location.reload();
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    // Add comment to the page dynamically for logged users
                    if (data.comment && data.comment.is_approved) {
                        const commentsContainer = document.getElementById('comments-list');
                        const commentHtml = `
                            <div class="flex gap-4 mb-6">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        ${data.comment.author_name.substring(0, 2).toUpperCase()}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">${data.comment.author_name}</h4>
                                                ${data.comment.is_from_logged_user ? '<span class="px-2 py-1 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 text-xs rounded-full">Użytkownik</span>' : ''}
                                            </div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">teraz</span>
                                        </div>
                                        <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">${data.comment.content}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        commentsContainer.insertAdjacentHTML('afterbegin', commentHtml);
                        
                        // Update comment count
                        const commentCountElement = document.querySelector('h2:contains("Komentarze")');
                        if (commentCountElement) {
                            const currentCount = parseInt(commentCountElement.textContent.match(/\d+/)[0]);
                            commentCountElement.textContent = commentCountElement.textContent.replace(/\d+/, currentCount + 1);
                        }
                    }
                    
                    // Clear form
                    form.reset();
                    
                    // Show success message
                    const existingMessage = form.parentNode.querySelector('.success-message');
                    if (existingMessage) {
                        existingMessage.remove();
                    }
                    
                    const successMessage = document.createElement('div');
                    successMessage.className = 'success-message mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 dark:border-green-500 p-4';
                    successMessage.innerHTML = `
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700 dark:text-green-300">${data.message}</p>
                            </div>
                        </div>
                    `;
                    form.parentNode.insertBefore(successMessage, form);
                    
                    // Remove message after 5 seconds
                    setTimeout(() => {
                        successMessage.remove();
                    }, 5000);
                } else {
                    // If no JSON response, reload page (normal redirect)
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error submitting comment:', error);
                // On error, reload page to see any validation errors
                window.location.reload();
            })
            .finally(() => {
                // Restore button
                submitButton.innerHTML = originalButtonContent;
                submitButton.disabled = false;
            });
        });
    });
});
