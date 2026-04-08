<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Ustawienia powiadomień') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Zarządzaj swoimi preferencjami dotyczącymi powiadomień email.') }}
        </p>
    </header>

    @if (session('status') === 'notification-preferences-updated')
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-md mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 dark:text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Sukces!</h3>
                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">Ustawienia powiadomień zostały zapisane.</p>
                </div>
            </div>
        </div>
    @endif

    <form method="post" action="{{ route('profile.notifications') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-4">
            <!-- Global email notifications -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="email_notifications" name="email_notifications" type="checkbox" 
                           value="1" {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }}
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="email_notifications" class="font-medium text-gray-700">
                        Powiadomienia email (główne)
                    </label>
                    <p class="text-gray-500">Otrzymuj powiadomienia na adres email. Musi być włączone dla pozostałych opcji.</p>
                </div>
            </div>

            <!-- New posts from followed users -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="notify_new_posts" name="notify_new_posts" type="checkbox" 
                           value="1" {{ old('notify_new_posts', $user->notify_new_posts) ? 'checked' : '' }}
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="notify_new_posts" class="font-medium text-gray-700">
                        Nowe posty od obserwowanych
                    </label>
                    <p class="text-gray-500">Powiadamiaj o nowych postach od użytkowników, których obserwujesz.</p>
                </div>
            </div>

            <!-- New private messages -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="notify_messages" name="notify_messages" type="checkbox" 
                           value="1" {{ old('notify_messages', $user->notify_messages) ? 'checked' : '' }}
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="notify_messages" class="font-medium text-gray-700">
                        Nowe wiadomości prywatne
                    </label>
                    <p class="text-gray-500">Powiadamiaj o nowych wiadomościach od znajomych w czacie.</p>
                </div>
            </div>

            <!-- Friend requests -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="notify_friend_requests" name="notify_friend_requests" type="checkbox" 
                           value="1" {{ old('notify_friend_requests', $user->notify_friend_requests) ? 'checked' : '' }}
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="notify_friend_requests" class="font-medium text-gray-700">
                        Zaproszenia do znajomych
                    </label>
                    <p class="text-gray-500">Powiadamiaj o nowych zaproszeniach do znajomych.</p>
                </div>
            </div>
        </div>

        @if($user->muted_users && count($user->muted_users) > 0)
            <div class="border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Wyciszeni użytkownicy</h3>
                <div class="space-y-2">
                    @foreach(App\Models\User::whereIn('id', $user->muted_users)->get() as $mutedUser)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-700">{{ $mutedUser->name }}</span>
                            <button type="button" 
                                    onclick="toggleMute({{ $mutedUser->id }})"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                Odcisz
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (!$user->hasVerifiedEmail())
            <div class="rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Email nie został zweryfikowany
                        </h3>
                        <p class="mt-1 text-sm text-yellow-700">
                            Aby otrzymywać powiadomienia, musisz najpierw zweryfikować swój adres email.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Zapisz ustawienia') }}</x-primary-button>

            @if (session('status') === 'notification-preferences-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">{{ __('Zapisano.') }}</p>
            @endif
        </div>
    </form>

    <script>
        function toggleMute(userId) {
            fetch(`/users/${userId}/mute`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simple refresh to update the UI
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</section>