<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Znajomi</h1>
                <p class="text-gray-600 dark:text-gray-400">Zarządzaj swoimi znajomościami</p>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6 overflow-x-auto">
                <nav class="-mb-px flex space-x-6 min-w-max">
                    <button onclick="switchTab('friends')" id="friends-tab" 
                            class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                        Znajomi ({{ $friends->count() }})
                    </button>
                    <button onclick="switchTab('pending')" id="pending-tab" 
                            class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                        Oczekujące zaproszenia ({{ $pendingRequests->count() }})
                    </button>
                    <button onclick="switchTab('sent')" id="sent-tab" 
                            class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                        Wysłane zaproszenia ({{ $sentRequests->count() }})
                    </button>
                </nav>
            </div>

            <!-- Friends Tab -->
            <div id="friends-content" class="tab-content">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Twoi znajomi</h2>
                        
                        @if($friends->count() > 0)
                            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                @foreach($friends as $friend)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                                    <a href="{{ route('users.profile', $friend) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                        {{ $friend->name }}
                                                    </a>
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $friend->email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="#" onclick="startConversation({{ $friend->id }})"
                                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                                Napisz
                                            </a>
                                            <button onclick="removeFriend({{ $friend->id }})" 
                                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm">
                                                Usuń
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">Nie masz jeszcze znajomych.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pending Requests Tab -->
            <div id="pending-content" class="tab-content hidden">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Zaproszenia do znajomości</h2>
                        
                        @if($pendingRequests->count() > 0)
                            <div class="space-y-4">
                                @foreach($pendingRequests as $request)
                                    @php
                                        $requester = $request->requester;
                                    @endphp
                                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $requester->name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $requester->email }}</p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                                    Wysłano {{ $request->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="acceptFriendship({{ $request->id }})" 
                                                    class="px-3 py-1 bg-green-600 dark:bg-green-700 text-white text-sm rounded-md hover:bg-green-700 dark:hover:bg-green-600">
                                                Akceptuj
                                            </button>
                                            <button onclick="rejectFriendship({{ $request->id }})" 
                                                    class="px-3 py-1 bg-red-600 dark:bg-red-700 text-white text-sm rounded-md hover:bg-red-700 dark:hover:bg-red-600">
                                                Odrzuć
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">Brak oczekujących zaproszeń.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sent Requests Tab -->
            <div id="sent-content" class="tab-content hidden">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Wysłane zaproszenia</h2>
                        
                        @if($sentRequests->count() > 0)
                            <div class="space-y-4">
                                @foreach($sentRequests as $request)
                                    @php
                                        $addressee = $request->addressee;
                                    @endphp
                                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $addressee->name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $addressee->email }}</p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                                    Wysłano {{ $request->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 text-xs rounded-full">
                                                Oczekuje
                                            </span>
                                            <button onclick="cancelFriendshipRequest({{ $request->id }})" 
                                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm">
                                                Anuluj
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">Brak wysłanych zaproszeń.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected content
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Update tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            const selectedTab = document.getElementById(tabName + '-tab');
            selectedTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            selectedTab.classList.add('border-indigo-500', 'text-indigo-600');
        }

        async function acceptFriendship(friendshipId) {
            try {
                const response = await fetch(`/friendships/${friendshipId}/accept`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Błąd podczas akceptowania zaproszenia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas akceptowania zaproszenia');
            }
        }

        async function rejectFriendship(friendshipId) {
            try {
                const response = await fetch(`/friendships/${friendshipId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Błąd podczas odrzucania zaproszenia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas odrzucania zaproszenia');
            }
        }

        async function cancelFriendshipRequest(friendshipId) {
            if (!confirm('Czy na pewno chcesz anulować to zaproszenie?')) return;

            try {
                const response = await fetch(`/friendships/${friendshipId}/cancel`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Błąd podczas anulowania zaproszenia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas anulowania zaproszenia');
            }
        }

        async function removeFriend(userId) {
            if (!confirm('Czy na pewno chcesz usunąć tę osobę ze znajomych?')) return;

            try {
                const response = await fetch(`/users/${userId}/friend`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Błąd podczas usuwania znajomego');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas usuwania znajomego');
            }
        }

        function startConversation(userId) {
            // Create form and submit to start conversation
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("conversations.create") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'user_id';
            userIdInput.value = userId;
            
            form.appendChild(csrfToken);
            form.appendChild(userIdInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Initialize with friends tab
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('friends');
        });
    </script>
</x-app-layout>
