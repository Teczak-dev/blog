<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Odkryj użytkowników</h1>
                <p class="text-gray-600 mt-2">Znajdź ciekawych autorów do obserwowania i dodawania do znajomych</p>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6 bg-white rounded-lg shadow-sm p-6">
                <form method="GET" action="{{ route('users.index') }}" class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                Szukaj użytkowników
                            </label>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Wpisz imię lub email..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="sm:w-48">
                            <label for="verified" class="block text-sm font-medium text-gray-700 mb-1">
                                Status weryfikacji
                            </label>
                            <select name="verified" 
                                    id="verified"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Wszyscy użytkownicy</option>
                                <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Tylko zweryfikowani</option>
                            </select>
                        </div>
                        <div class="self-end">
                            <button type="submit" 
                                    class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Szukaj
                            </button>
                        </div>
                    </div>
                    @if(request('search') || request('verified'))
                        <div class="text-sm text-gray-600">
                            <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">
                                ← Wyczyść filtry
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Users Grid -->
            @if($users->count() > 0)
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($users as $user)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
                            <!-- User Avatar -->
                            <div class="text-center mb-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold text-white">{{ $user->name[0] }}</span>
                                </div>
                                
                                <h3 class="text-xl font-semibold text-gray-900">
                                    <a href="{{ route('users.profile', $user) }}" class="hover:text-blue-600">
                                        {{ $user->name }}
                                    </a>
                                </h3>
                                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                                
                                <!-- Verification Status -->
                                <div class="mt-2">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Zweryfikowany
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L5.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                            Niezweryfikowany
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- User Stats -->
                            <div class="flex justify-around mb-6 text-center border-t border-b border-gray-100 py-4">
                                <div>
                                    <span class="block text-2xl font-bold text-gray-900">{{ $user->posts_count }}</span>
                                    <span class="text-xs text-gray-500 uppercase">{{ $user->posts_count == 1 ? 'Post' : 'Postów' }}</span>
                                </div>
                                <div>
                                    <span class="block text-2xl font-bold text-gray-900">{{ $user->followers_count }}</span>
                                    <span class="text-xs text-gray-500 uppercase">Obserwujących</span>
                                </div>
                                <div>
                                    <span class="block text-2xl font-bold text-gray-900">{{ $user->following_count }}</span>
                                    <span class="text-xs text-gray-500 uppercase">Obserwuje</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            @auth
                                <div class="flex space-x-2">
                                    @php
                                        $currentUser = Auth::user();
                                        $isFollowing = $currentUser->isFollowing($user);
                                        $friendship = \App\Models\Friendship::where(function ($query) use ($currentUser, $user) {
                                            $query->where('requester_id', $currentUser->id)->where('addressee_id', $user->id);
                                        })->orWhere(function ($query) use ($currentUser, $user) {
                                            $query->where('requester_id', $user->id)->where('addressee_id', $currentUser->id);
                                        })->first();
                                        
                                        $canSendFriendRequest = !$friendship;
                                        $canAcceptFriendRequest = $friendship && $friendship->status === 'pending' && $friendship->addressee_id === $currentUser->id;
                                        $isFriend = $friendship && $friendship->status === 'accepted';
                                        $requestSent = $friendship && $friendship->status === 'pending' && $friendship->requester_id === $currentUser->id;
                                    @endphp

                                    <!-- Follow Button -->
                                    <button 
                                        onclick="toggleFollow({{ $user->id }}, this)"
                                        class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $isFollowing ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                        {{ $isFollowing ? 'Przestań obserwować' : 'Obserwuj' }}
                                    </button>

                                    <!-- Friend Button -->
                                    @if($isFriend)
                                        <button class="flex-1 px-3 py-2 text-sm font-medium bg-green-100 text-green-700 rounded-md">
                                            ✓ Znajomy
                                        </button>
                                    @elseif($canAcceptFriendRequest)
                                        <button 
                                            onclick="acceptFriendRequest({{ $user->id }}, this)"
                                            class="flex-1 px-3 py-2 text-sm font-medium bg-green-600 text-white rounded-md hover:bg-green-700">
                                            Zaakceptuj
                                        </button>
                                    @elseif($requestSent)
                                        <button class="flex-1 px-3 py-2 text-sm font-medium bg-yellow-100 text-yellow-700 rounded-md">
                                            Oczekuje
                                        </button>
                                    @elseif($canSendFriendRequest)
                                        <button 
                                            onclick="sendFriendRequest({{ $user->id }}, this)"
                                            class="flex-1 px-3 py-2 text-sm font-medium bg-green-600 text-white rounded-md hover:bg-green-700">
                                            Zaproszenie
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="text-center">
                                    <a href="{{ route('login') }}" 
                                       class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                        Zaloguj się, aby obserwować
                                    </a>
                                </div>
                            @endauth
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="mt-8">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak użytkowników</h3>
                    <p class="text-gray-500">Nie ma jeszcze zweryfikowanych użytkowników.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        async function toggleFollow(userId, button) {
            const isFollowing = button.textContent.trim().includes('Przestań');
            
            try {
                const response = await fetch(`/users/${userId}/${isFollowing ? 'unfollow' : 'follow'}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    if (isFollowing) {
                        button.className = 'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-blue-600 text-white hover:bg-blue-700';
                        button.textContent = 'Obserwuj';
                    } else {
                        button.className = 'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                        button.textContent = 'Przestań obserwować';
                    }
                } else {
                    alert('Błąd podczas zmiany obserwowania');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas zmiany obserwowania');
            }
        }

        async function sendFriendRequest(userId, button) {
            try {
                const response = await fetch(`/users/${userId}/friend-request`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    button.className = 'flex-1 px-3 py-2 text-sm font-medium bg-yellow-100 text-yellow-700 rounded-md';
                    button.textContent = 'Oczekuje';
                    button.onclick = null;
                } else {
                    alert('Błąd podczas wysyłania zaproszenia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas wysyłania zaproszenia');
            }
        }

        async function acceptFriendRequest(userId, button) {
            // We need to find the friendship ID first
            try {
                const response = await fetch(`/users/${userId}/friend-request`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    button.className = 'flex-1 px-3 py-2 text-sm font-medium bg-green-100 text-green-700 rounded-md';
                    button.textContent = '✓ Znajomy';
                    button.onclick = null;
                } else {
                    alert('Błąd podczas akceptowania zaproszenia');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Błąd podczas akceptowania zaproszenia');
            }
        }
    </script>
</x-app-layout>