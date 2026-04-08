<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Ustawienia powiadomień') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Zarządzaj preferencjami powiadomień email dotyczących aktywności na Twoich postach.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.notifications.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="email_notifications" 
                        name="email_notifications" 
                        type="checkbox" 
                        value="1"
                        {{ $user->email_notifications ? 'checked' : '' }}
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="email_notifications" class="font-medium text-gray-700">
                        Powiadomienia o nowych komentarzach
                    </label>
                    <p class="text-gray-500">
                        Otrzymuj powiadomienia email gdy ktoś skomentuje Twoje posty.
                    </p>
                </div>
            </div>

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
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Zapisz ustawienia') }}</x-primary-button>

            @if (session('status') === 'notifications-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Zapisano.') }}</p>
            @endif
        </div>
    </form>
</section>