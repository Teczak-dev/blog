    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        📝 Blog
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('posts.index') }}"
                        class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Home
                    </a>
                    <a href="{{ route('posts.create') }}"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                        Nowy Post
                    </a>
                </div>
            </div>
        </div>
    </nav>
