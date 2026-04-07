<x-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    📝 Najnowsze Posty
                </h1>
                <p class="text-xl text-indigo-100 mb-8 max-w-3xl mx-auto">
                    Odkryj najnowsze artykuły z świata programowania, technologii i rozwoju osobistego
                </p>
                @auth
                    <a href="{{ route('posts.create') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-indigo-600 font-bold rounded-2xl hover:bg-gray-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Napisz nowy post
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-indigo-600 font-bold rounded-2xl hover:bg-gray-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:scale-105">
                        🚀 Zacznij pisać
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Success Messages -->
        @if (session('success'))
            <div class="mb-8 bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="mb-8">
            <form method="GET" action="{{ route('posts.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Szukaj postów po tytule, treści, kategorii, autorze, tagach..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 shadow-sm">
                    </div>
                    <button type="submit" 
                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                        🔍 Szukaj
                    </button>
                </div>
                
                <!-- Advanced Filters -->
                <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🎯 Filtry zaawansowane</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Wszystkie kategorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Author Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Autor</label>
                            <select name="author" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Wszyscy autorzy</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author }}" {{ request('author') === $author ? 'selected' : '' }}>
                                        {{ $author }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Tag Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tag</label>
                            <select name="tag" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Wszystkie tagi</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag }}" {{ request('tag') === $tag ? 'selected' : '' }}>
                                        #{{ $tag }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Date Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data od</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data do</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <!-- Reading Time Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Czas czytania (min)</label>
                            <div class="flex gap-2">
                                <input type="number" name="read_time_min" value="{{ request('read_time_min') }}" 
                                       placeholder="Od" min="1" max="60"
                                       class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <input type="number" name="read_time_max" value="{{ request('read_time_max') }}" 
                                       placeholder="Do" min="1" max="60"
                                       class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Zastosuj filtry
                        </button>
                        
                        @if(request()->hasAny(['category', 'author', 'tag', 'date_from', 'date_to', 'read_time_min', 'read_time_max', 'search']))
                            <a href="{{ route('posts.index') }}" 
                               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Wyczyść wszystkie
                            </a>
                        @endif
                    </div>
                </div>
                
                @if(request('search'))
                    <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <span class="text-blue-800 font-medium">
                                📝 Wyniki dla: "{{ request('search') }}"
                            </span>
                            <span class="text-blue-600">
                                ({{ $posts->total() }} {{ $posts->total() == 1 ? 'post' : 'postów' }})
                            </span>
                        </div>
                        <a href="{{ route('posts.index') }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            ✖️ Wyczyść
                        </a>
                    </div>
                @endif
                
                @if(request()->hasAny(['category', 'author', 'tag', 'date_from', 'date_to', 'read_time_min', 'read_time_max']))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-green-800 font-medium">🎯 Aktywne filtry:</span>
                                <div class="flex flex-wrap gap-2">
                                    @if(request('category'))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">{{ request('category') }}</span>
                                    @endif
                                    @if(request('author'))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">{{ request('author') }}</span>
                                    @endif
                                    @if(request('tag'))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">#{{ request('tag') }}</span>
                                    @endif
                                    @if(request('date_from') || request('date_to'))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                            {{ request('date_from', '...') }} - {{ request('date_to', '...') }}
                                        </span>
                                    @endif
                                    @if(request('read_time_min') || request('read_time_max'))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                            {{ request('read_time_min', '0') }}-{{ request('read_time_max', '∞') }} min
                                        </span>
                                    @endif
                                </div>
                                <span class="text-green-600">({{ $posts->total() }} {{ $posts->total() == 1 ? 'post' : 'postów' }})</span>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Posts Grid -->
        @if($posts->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $item)
                <!-- Post Card X -->
                <article
                    class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        @if ($item->photo)
                            <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-6xl">📝</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <!-- Category -->
                            @if($item->category)
                                <span class="px-4 py-1 {{ $item->getCategoryColorClasses() }} text-xs font-semibold rounded-full">
                                    {{ $item->category }}
                                </span>
                            @endif
                            
                            <!-- Read time -->
                            <span class="text-gray-500 text-sm ml-auto">{{ $item->read_time_minutes ?? 5 }} min czytania</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 cursor-pointer">
                            <a href="{{ route('posts.show', $item->id) }}">{{ $item->title }}</a>
                        </h3>
                        <div class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {!! $item->lead ?? Str::limit(strip_tags($item->content), 150) !!}
                        </div>
                        
                        <!-- Hashtags -->
                        @if($item->tags && count($item->tags) > 0)
                            <div class="mb-4 flex flex-wrap gap-2">
                                @foreach(array_slice($item->tags, 0, 3) as $tag)
                                    <span class="text-indigo-600 text-sm font-medium">
                                        #{{ strtolower(str_replace(' ', '', $tag)) }}
                                    </span>
                                @endforeach
                                @if(count($item->tags) > 3)
                                    <span class="text-gray-400 text-sm">
                                        +{{ count($item->tags) - 3 }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                    {{ $item->author[0] }}
                                </div>
                                <span class="text-sm text-gray-700 font-medium">{{ $item->author }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </article>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <span class="text-4xl">📝</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak postów</h3>
                <p class="text-gray-600 mb-6 max-w-sm mx-auto">
                    Nie ma jeszcze żadnych postów do wyświetlenia. 
                    @if (auth()->check())
                        Napisz pierwszy post!
                    @else
                        Zaloguj się, aby napisać pierwszy post!
                    @endif
                </p>
                @if (auth()->check())
                    <a href="{{ route('posts.create') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        ✍️ Napisz pierwszy post
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        🔑 Zaloguj się
                    </a>
                    </a>
                @endif
            </div>
        @endif



        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="mt-12">
                {{ $posts->links('vendor.pagination.custom') }}
            </div>
        @endif
    </main>



</x-layout>
