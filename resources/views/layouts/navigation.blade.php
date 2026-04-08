<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        🏠 Panel
                    </x-nav-link>
                    <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">
                        📝 Posty
                    </x-nav-link>
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        👤 Użytkownicy
                    </x-nav-link>
                    <x-nav-link :href="route('friends.index')" :active="request()->routeIs('friends.*')">
                        👥 Znajomi
                    </x-nav-link>
                    <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*')">
                        💬 Wiadomości
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Notifications Dropdown -->
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a5 5 0 00-10 0v5l-5 5h5m5 0a2 2 0 01-2 2 2 2 0 01-2-2m4 0H9m6 0V9a1 1 0 00-1-1H8a1 1 0 00-1 1v8"/>
                            </svg>
                            
                            @php
                                $totalNotifications = Auth::user()->getUnreadMessagesCount() + Auth::user()->getPendingFriendRequestsCount();
                            @endphp
                            
                            @if($totalNotifications > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full min-w-[20px] h-5 flex items-center justify-center font-medium">
                                    {{ $totalNotifications > 99 ? '99+' : $totalNotifications }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="py-2">
                            <div class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                Powiadomienia
                            </div>
                            
                            <!-- Unread Messages -->
                            @php $unreadMessages = Auth::user()->getUnreadMessagesCount(); @endphp
                            @if($unreadMessages > 0)
                                <x-dropdown-link :href="route('conversations.index')" class="flex items-center justify-between">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                        Nowe wiadomości
                                    </span>
                                    <span class="bg-blue-500 text-white text-xs rounded-full px-2 py-0.5">
                                        {{ $unreadMessages }}
                                    </span>
                                </x-dropdown-link>
                            @endif
                            
                            <!-- Pending Friend Requests -->
                            @php $pendingRequests = Auth::user()->getPendingFriendRequestsCount(); @endphp
                            @if($pendingRequests > 0)
                                <x-dropdown-link :href="route('friends.index')" class="flex items-center justify-between">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        Zaproszenia do znajomych
                                    </span>
                                    <span class="bg-green-500 text-white text-xs rounded-full px-2 py-0.5">
                                        {{ $pendingRequests }}
                                    </span>
                                </x-dropdown-link>
                            @endif
                            
                            @if($totalNotifications === 0)
                                <div class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a5 5 0 00-10 0v5l-5 5h5m5 0a2 2 0 01-2 2 2 2 0 01-2-2m4 0H9m6 0V9a1 1 0 00-1-1H8a1 1 0 00-1 1v8"/>
                                    </svg>
                                    Brak nowych powiadomień
                                </div>
                            @endif
                            
                            <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                                <x-dropdown-link :href="route('conversations.index')">
                                    Zobacz wszystkie wiadomości
                                </x-dropdown-link>
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
                
                <!-- User Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                🏠 Panel
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Notifications section for mobile -->
                @php
                    $unreadMessages = Auth::user()->getUnreadMessagesCount();
                    $pendingRequests = Auth::user()->getPendingFriendRequestsCount();
                    $totalNotifications = $unreadMessages + $pendingRequests;
                @endphp
                
                @if($totalNotifications > 0)
                    <div class="px-4 py-2">
                        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Powiadomienia</div>
                        
                        @if($unreadMessages > 0)
                            <x-responsive-nav-link :href="route('conversations.index')" class="flex items-center justify-between">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    Nowe wiadomości
                                </span>
                                <span class="bg-blue-500 text-white text-xs rounded-full px-2 py-0.5">
                                    {{ $unreadMessages }}
                                </span>
                            </x-responsive-nav-link>
                        @endif
                        
                        @if($pendingRequests > 0)
                            <x-responsive-nav-link :href="route('friends.index')" class="flex items-center justify-between">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    Zaproszenia
                                </span>
                                <span class="bg-green-500 text-white text-xs rounded-full px-2 py-0.5">
                                    {{ $pendingRequests }}
                                </span>
                            </x-responsive-nav-link>
                        @endif
                    </div>
                @endif
                
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
