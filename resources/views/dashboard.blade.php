<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🏠 Panel Użytkownika
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Dashboard Card -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                <!-- Header with Gradient -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 px-8 py-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">🏠</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">🏠 Panel</h1>
                            <p class="text-indigo-100 dark:text-indigo-200">Witaj ponownie, {{ auth()->user()->name }}!</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Twój Blog</h2>
                        <p class="text-gray-600 dark:text-gray-400">Zarządzaj swoimi postami i twórz nowe treści.</p>
                    </div>
                    
                    <!-- Action Cards -->
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <!-- View Posts Card -->
                        <a href="{{ route('posts.index') }}" 
                           class="group p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 hover:shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">📚</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-blue-900 dark:text-blue-300">Zobacz Posty</h3>
                                    <p class="text-sm text-blue-600 dark:text-blue-400">Przeglądaj wszystkie</p>
                                </div>
                            </div>
                            <p class="text-blue-700 dark:text-blue-300">Sprawdź wszystkie opublikowane posty w blogu.</p>
                        </a>

                        <!-- Create Post Card -->
                        <a href="{{ route('posts.create') }}" 
                           class="group p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-700 hover:border-green-300 dark:hover:border-green-600 transition-all duration-200 hover:shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-green-600 dark:bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">✍️</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-green-900 dark:text-green-300">Nowy Post</h3>
                                    <p class="text-sm text-green-600 dark:text-green-400">Stwórz artykuł</p>
                                </div>
                            </div>
                            <p class="text-green-700 dark:text-green-300">Napisz i opublikuj nowy post w swoim blogu.</p>
                        </a>

                        <!-- Users Discovery Card -->
                        <a href="{{ route('users.index') }}" 
                           class="group p-6 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl border border-amber-200 dark:border-amber-700 hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-200 hover:shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-amber-600 dark:bg-amber-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">👤</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-amber-900 dark:text-amber-300">Użytkownicy</h3>
                                    <p class="text-sm text-amber-600 dark:text-amber-400">Odkryj ludzi</p>
                                </div>
                            </div>
                            <p class="text-amber-700 dark:text-amber-300">Znajdź nowych autorów do obserwowania.</p>
                        </a>

                        <!-- Friends Card -->
                        <a href="{{ route('friends.index') }}" 
                           class="group p-6 bg-gradient-to-br from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 rounded-xl border border-rose-200 dark:border-rose-700 hover:border-rose-300 dark:hover:border-rose-600 transition-all duration-200 hover:shadow-lg relative">
                            @if(auth()->user()->getPendingFriendRequestsCount() > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">
                                    {{ auth()->user()->getPendingFriendRequestsCount() }}
                                </span>
                            @endif
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-rose-600 dark:bg-rose-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">👥</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-rose-900 dark:text-rose-300">Znajomi</h3>
                                    <p class="text-sm text-rose-600 dark:text-rose-400">Zarządzaj relacjami</p>
                                </div>
                            </div>
                            <p class="text-rose-700 dark:text-rose-300">Sprawdź zaproszenia i znajomych.</p>
                        </a>

                        <!-- Messages Card -->
                        <a href="{{ route('conversations.index') }}" 
                           class="group p-6 bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl border border-cyan-200 dark:border-cyan-700 hover:border-cyan-300 dark:hover:border-cyan-600 transition-all duration-200 hover:shadow-lg relative">
                            @if(auth()->user()->getUnreadMessagesCount() > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">
                                    {{ auth()->user()->getUnreadMessagesCount() }}
                                </span>
                            @endif
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-cyan-600 dark:bg-cyan-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">💬</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-cyan-900 dark:text-cyan-300">Wiadomości</h3>
                                    <p class="text-sm text-cyan-600 dark:text-cyan-400">Rozmowy prywatne</p>
                                </div>
                            </div>
                            <p class="text-cyan-700 dark:text-cyan-300">Sprawdź nowe wiadomości od znajomych.</p>
                        </a>

                        <!-- Profile Card -->
                        <a href="{{ route('profile.edit') }}" 
                           class="group p-6 bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-xl border border-purple-200 dark:border-purple-700 hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-200 hover:shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-purple-600 dark:bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-xl text-white">⚙️</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-purple-900 dark:text-purple-300">Ustawienia</h3>
                                    <p class="text-sm text-purple-600 dark:text-purple-400">Twoje konto</p>
                                </div>
                            </div>
                            <p class="text-purple-700 dark:text-purple-300">Edytuj profil i preferencje powiadomień.</p>
                        </a>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Twoje statystyki</h3>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ \App\Models\Post::where('user_id', auth()->id())->count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Twoje posty</div>
                            </div>
                            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ auth()->user()->followers()->count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Obserwujących</div>
                            </div>
                            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ auth()->user()->following()->count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Obserwujesz</div>
                            </div>
                            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ auth()->user()->getFriends()->count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Znajomi</div>
                            </div>
                        </div>
                        
                        <!-- Global Stats -->
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mt-6 mb-4">Statystyki platformy</h4>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ \App\Models\Post::count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Wszystkie posty</div>
                            </div>
                            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Użytkownicy</div>
                            </div>
                            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-teal-600 dark:text-teal-400">{{ \App\Models\Comment::count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Komentarze</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
