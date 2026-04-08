<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Znajomi {{ $user->name }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $friends->count() }} znajomych</p>
                </div>
                
                <div class="p-6">
                    @if($friends->count() > 0)
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($friends as $friend)
                                <div class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-lg">
                                    <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('users.profile', $friend) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $friend->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $friend->email }}</p>
                                    </div>
                                    @auth
                                        @if(Auth::id() === $user->id)
                                            <div class="flex space-x-2">
                                                <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                                    Napisz
                                                </a>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">{{ $user->name }} nie ma jeszcze znajomych.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>