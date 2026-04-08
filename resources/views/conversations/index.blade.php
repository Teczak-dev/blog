<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden min-h-[70vh] md:h-screen md:max-h-[80vh]">
                <div class="flex flex-col md:flex-row h-full">
                    <!-- Conversations Sidebar -->
                    <div class="w-full md:w-80 md:min-w-80 border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-700 flex flex-col">
                        <!-- Header -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Wiadomości</h1>
                            <button onclick="showNewConversationModal()" 
                                    class="mt-3 w-full bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nowa rozmowa
                                </div>
                            </button>
                        </div>

                        <!-- Conversations List -->
                        <div class="flex-1 overflow-y-auto">
                            @forelse($conversations as $conversation)
                                <div class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <a href="{{ route('conversations.show', $conversation) }}" 
                                       class="block p-4 {{ request()->route('conversation')?->id === $conversation->id ? 'bg-blue-50 dark:bg-blue-900/30 border-r-2 border-blue-500' : '' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h3 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                                    @if($conversation->title)
                                                        {{ $conversation->title }}
                                                    @else
                                                        @php
                                                            $otherParticipant = $conversation->participants->where('id', '!=', auth()->id())->first();
                                                        @endphp
                                                        {{ $otherParticipant ? $otherParticipant->name : 'Nieznany użytkownik' }}
                                                    @endif
                                                </h3>
                                                @if($conversation->messages->isNotEmpty())
                                                    <p class="text-sm text-gray-500 truncate mt-1">
                                                        {{ $conversation->messages->last()->content }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right ml-2">
                                                @if($conversation->messages->isNotEmpty())
                                                    <span class="text-xs text-gray-400">
                                                        {{ $conversation->messages->last()->created_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                                @php
                                                    $unreadCount = $conversation->getUnreadMessagesCount(auth()->id());
                                                @endphp
                                                @if($unreadCount > 0)
                                                    <div class="bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center mt-1 ml-auto">
                                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.456L3 21l2.456-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                                    </svg>
                                    <p>Brak rozmów</p>
                                    <p class="text-sm mt-1">Rozpocznij nową rozmowę</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col min-h-[50vh]">
                        @if(isset($currentConversation))
                            @include('conversations.chat', ['conversation' => $currentConversation])
                        @else
                            <!-- Welcome Screen -->
                            <div class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.456L3 21l2.456-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Wybierz rozmowę</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Wybierz rozmowę z listy lub rozpocznij nową</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Conversation Modal -->
    <div id="newConversationModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Nowa rozmowa</h3>
                        
                        <form id="newConversationForm" method="POST" action="{{ route('conversations.create') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Wybierz użytkownika:</label>
                                <select name="user_id" id="user_id" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Wybierz użytkownika --</option>
                                    @foreach($allUsers->where('id', '!=', auth()->id()) as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="hideNewConversationModal()" 
                                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Anuluj
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Rozpocznij rozmowę
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showNewConversationModal() {
            document.getElementById('newConversationModal').classList.remove('hidden');
        }

        function hideNewConversationModal() {
            document.getElementById('newConversationModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
