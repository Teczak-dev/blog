<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Profile Header -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
                <div class="p-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <!-- Profile Image Placeholder -->
                            <div class="w-24 h-24 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            
                            <!-- User Info -->
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h1>
                                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $user->email }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    Dołączył {{ $user->created_at->format('d.m.Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @auth
                            @if(Auth::id() !== $user->id)
                                <div class="flex space-x-3">
                                    <!-- Follow Button -->
                                    <button 
                                        id="follow-btn"
                                        onclick="toggleFollow({{ $user->id }})"
                                        class="px-4 py-2 rounded-md font-medium transition-colors duration-200 {{ $isFollowing ? 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500' : 'bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600' }}">
                                        <span id="follow-text">{{ $isFollowing ? 'Przestań obserwować' : 'Obserwuj' }}</span>
                                    </button>

                                    <!-- Friend Request Button -->
                                    @if($canSendFriendRequest)
                                        <button 
                                            onclick="sendFriendRequest({{ $user->id }})"
                                            class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 font-medium transition-colors duration-200">
                                            Wyślij zaproszenie
                                        </button>
                                    @elseif($canAcceptFriendRequest)
                                        <button 
                                            onclick="acceptFriendRequest({{ $user->id }})"
                                            class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 font-medium transition-colors duration-200">
                                            Zaakceptuj zaproszenie
                                        </button>
                                    @elseif($friendshipStatus === 'accepted')
                                        <a href="{{ route('conversations.create-private', $user) }}" 
                                           class="px-4 py-2 bg-purple-600 dark:bg-purple-500 text-white rounded-md hover:bg-purple-700 dark:hover:bg-purple-600 font-medium transition-colors duration-200">
                                            Napisz wiadomość
                                        </a>
                                    @elseif($friendshipStatus === 'pending')
                                        <span class="px-4 py-2 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-md font-medium">
                                            Zaproszenie wysłane
                                        </span>
                                    @endif
                                </div>
                            @endif
                        @endauth
                    </div>

                    <!-- Stats -->
                    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->posts_count ?? 0 }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Postów</div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('users.followers', $user) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 transition-colors">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->followers_count ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Obserwujących</div>
                            </a>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('users.following', $user) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 transition-colors">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->following_count ?? 0 }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Obserwowanych</div>
                            </a>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('users.friends', $user) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 transition-colors">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->sentFriendRequests()->where('status', 'accepted')->count() + $user->receivedFriendRequests()->where('status', 'accepted')->count() }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Znajomych</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User's Posts -->
            @if($posts->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Najnowsze posty</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($posts as $post)
                                <article class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden hover:shadow-md transition-shadow bg-white dark:bg-gray-700">
                                    @if($post->photo)
                                        <img src="{{ asset('storage/' . $post->photo) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                                    @endif
                                    <div class="p-4">
                                        <span class="inline-block px-2 py-1 text-xs font-medium bg-{{ $post->category_color }}-100 dark:bg-{{ $post->category_color }}-900 text-{{ $post->category_color }}-800 dark:text-{{ $post->category_color }}-200 rounded">
                                            {{ $post->category }}
                                        </span>
                                        <h3 class="mt-2 font-semibold text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                            <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                                        </h3>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">{{ $post->lead }}</p>
                                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $post->created_at->format('d.m.Y') }} • {{ $post->read_time_minutes }} min
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if($posts->hasPages())
                            <div class="mt-6">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">{{ $user->name }} nie opublikował jeszcze żadnych postów.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleFollow(userId) {
            fetch(`/users/${userId}/follow`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = document.getElementById('follow-btn');
                    const text = document.getElementById('follow-text');
                    
                    if (data.is_following) {
                        btn.className = 'px-4 py-2 rounded-md font-medium transition-colors duration-200 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500';
                        text.textContent = 'Przestań obserwować';
                    } else {
                        btn.className = 'px-4 py-2 rounded-md font-medium transition-colors duration-200 bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600';
                        text.textContent = 'Obserwuj';
                    }
                    
                    // Update followers count
                    location.reload(); // Simple refresh for now
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function sendFriendRequest(userId) {
            fetch(`/users/${userId}/friend-request`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function acceptFriendRequest(userId) {
            // For now, we'll reload. In a full implementation, we'd find the specific friendship ID
            location.reload();
        }
    </script>
</x-app-layout>