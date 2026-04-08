<!-- Chat Header -->
<div class="p-6 border-b border-gray-200 bg-white">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h2 class="font-medium text-gray-900">
                    @if($conversation->title)
                        {{ $conversation->title }}
                    @else
                        @php
                            $otherParticipant = $conversation->participants->where('id', '!=', auth()->id())->first();
                        @endphp
                        {{ $otherParticipant ? $otherParticipant->name : 'Nieznany użytkownik' }}
                    @endif
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $conversation->participants->count() }} 
                    {{ $conversation->participants->count() === 1 ? 'uczestnik' : 'uczestników' }}
                </p>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <button onclick="markAllAsRead({{ $conversation->id }})" 
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" 
                    title="Oznacz jako przeczytane">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Messages Area -->
<div id="messagesContainer" class="flex-1 overflow-y-auto p-6 bg-gray-50 space-y-4">
    @forelse($conversation->messages->sortBy('created_at') as $message)
        <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200' }}">
                @if($message->user_id !== auth()->id())
                    <p class="text-xs font-medium text-gray-600 mb-1">{{ $message->user->name }}</p>
                @endif
                
                <p class="text-sm">{{ $message->content }}</p>
                
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs {{ $message->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                        {{ $message->created_at->format('H:i') }}
                    </span>
                    
                    @if($message->user_id === auth()->id())
                        <div class="flex items-center space-x-1 ml-2">
                            @if($message->is_read)
                                <svg class="w-3 h-3 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <svg class="w-3 h-3 text-blue-200 -ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-3 h-3 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.456L3 21l2.456-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
            </svg>
            <p class="text-gray-500">Brak wiadomości</p>
            <p class="text-sm text-gray-400 mt-1">Napisz pierwszą wiadomość poniżej</p>
        </div>
    @endforelse
</div>

<!-- Message Input -->
<div class="p-6 border-t border-gray-200 bg-white">
    <form id="messageForm" onsubmit="sendMessage(event, {{ $conversation->id }})" class="flex gap-3">
        @csrf
        <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
        
        <input type="text" 
               name="content" 
               id="messageInput"
               placeholder="Napisz wiadomość..." 
               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               required>
        
        <button type="submit" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </form>
</div>

<script>
async function sendMessage(event, conversationId) {
    event.preventDefault();
    
    const form = event.target;
    const messageInput = form.querySelector('#messageInput');
    const content = messageInput.value.trim();
    
    if (!content) return;
    
    try {
        const response = await fetch('{{ route("messages.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                content: content
            })
        });
        
        if (response.ok) {
            messageInput.value = '';
            // Refresh the page to show the new message
            window.location.reload();
        } else {
            alert('Błąd podczas wysyłania wiadomości');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Błąd podczas wysyłania wiadomości');
    }
}

async function markAllAsRead(conversationId) {
    try {
        const response = await fetch(`/conversations/${conversationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            // Refresh to update read status
            window.location.reload();
        }
    } catch (error) {
        console.error('Error marking as read:', error);
    }
}

// Auto-scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>