    <!-- Modern Navigation -->
    <nav class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📝</span>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">
                        Blog
                    </span>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('posts.index') }}"
                        class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-gray-700">
                        📚 Wszystkie Posty
                    </a>
                    
                    @auth
                        <a href="{{ route('users.index') }}"
                            class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-gray-700">
                            👤 Użytkownicy
                        </a>
                        <a href="{{ route('friends.index') }}"
                            class="relative text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50">
                            👥 Znajomi
                            @if(auth()->user()->getPendingFriendRequestsCount() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ auth()->user()->getPendingFriendRequestsCount() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('conversations.index') }}"
                            class="relative text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-gray-700">
                            💬 Wiadomości
                            @if(auth()->user()->getUnreadMessagesCount() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ auth()->user()->getUnreadMessagesCount() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('dashboard') }}"
                            class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-gray-700">
                            🏠 Dashboard
                        </a>
                        <a href="{{ route('posts.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 dark:hover:from-indigo-600 dark:hover:to-purple-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nowy Post
                        </a>
                        
                        <!-- Theme Switcher -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-gray-700" onclick="toggleThemeMenu()">
                                <svg id="theme-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- System icon by default -->
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Theme Dropdown -->
                            <div id="theme-dropdown" class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible transition-all duration-200 z-50">
                                <div class="py-2">
                                    <button onclick="setTheme('light')" class="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 w-full text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        Jasny
                                    </button>
                                    <button onclick="setTheme('dark')" class="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 w-full text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        Ciemny
                                    </button>
                                    <button onclick="setTheme('system')" class="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 w-full text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        System
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-gray-700">
                                <div class="w-8 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Profil
                                    </a>
                                    <div class="border-t border-gray-100 my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 w-full text-left">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Wyloguj
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-indigo-50 dark:hover:bg-gray-700">
                            Logowanie
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 dark:hover:from-indigo-600 dark:hover:to-purple-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Rejestracja
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('posts.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                    📚 Wszystkie Posty
                </a>
                @auth
                    <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                        👤 Użytkownicy
                    </a>
                    <a href="{{ route('friends.index') }}" class="relative block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                        👥 Znajomi
                        @if(auth()->user()->getPendingFriendRequestsCount() > 0)
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ auth()->user()->getPendingFriendRequestsCount() }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('conversations.index') }}" class="relative block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                        💬 Wiadomości
                        @if(auth()->user()->getUnreadMessagesCount() > 0)
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ auth()->user()->getUnreadMessagesCount() }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                        🏠 Dashboard
                    </a>
                    <a href="{{ route('posts.create') }}" class="block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                        ➕ Nowy Post
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                            👤 Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400">
                                🚪 Wyloguj
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                        🔐 Logowanie
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-indigo-600 dark:text-indigo-400 font-medium hover:bg-indigo-50 dark:hover:bg-gray-700">
                        📝 Rejestracja
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        function toggleThemeMenu() {
            const dropdown = document.getElementById('theme-dropdown');
            dropdown.classList.toggle('opacity-0');
            dropdown.classList.toggle('invisible');
        }

        // Close theme dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const themeDropdown = document.getElementById('theme-dropdown');
            const themeButton = e.target.closest('button[onclick="toggleThemeMenu()"]');
            
            if (!themeButton && !themeDropdown.contains(e.target)) {
                themeDropdown.classList.add('opacity-0');
                themeDropdown.classList.add('invisible');
            }
        });

        function setTheme(theme) {
            // Update UI immediately
            applyTheme(theme);
            
            // Save preference
            fetch('{{ route("theme.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ theme: theme })
            }).catch(error => {
                console.error('Error saving theme:', error);
            });

            // Update icon
            updateThemeIcon(theme);
            
            // Close dropdown
            const dropdown = document.getElementById('theme-dropdown');
            dropdown.classList.add('opacity-0');
            dropdown.classList.add('invisible');
        }

        function applyTheme(theme) {
            const html = document.documentElement;
            
            if (theme === 'dark') {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else if (theme === 'light') {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else { // system
                html.classList.remove('dark');
                localStorage.removeItem('theme');
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    html.classList.add('dark');
                }
            }
        }

        function updateThemeIcon(theme) {
            const icon = document.getElementById('theme-icon');
            if (theme === 'light') {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
            } else if (theme === 'dark') {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>';
            }
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            @auth
                const userTheme = '{{ auth()->user()->theme_preference ?? "system" }}';
                applyTheme(userTheme);
                updateThemeIcon(userTheme);
            @else
                const sessionTheme = '{{ session("theme_preference", "system") }}';
                applyTheme(sessionTheme);
                updateThemeIcon(sessionTheme);
            @endauth
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            const currentTheme = @auth '{{ auth()->user()->theme_preference ?? "system" }}' @else '{{ session("theme_preference", "system") }}' @endauth;
            if (currentTheme === 'system') {
                applyTheme('system');
            }
        });
    </script>
