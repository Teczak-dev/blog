<x-layout>
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Post Header -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <!-- Featured Image -->
            <div class="h-96 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                {{-- <span class="text-9xl">{{ $post->photo }}</span> --}}
                <img src="{{ Storage::url($post->photo) }}" alt="{{ $post->title }}" class="object-cover h-full w-full">
                
            </div>

            <!-- Post Content -->
            <div class="p-8">
                <a href="{{ route('posts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
                    &larr; Powrót do listy artykułów
                </a>
                <a href="{{ route('posts.edit', $post->slug) }}" class="text-sm text-blue-600 hover:text-blue-800 mb-4 inline-block">
                    Edytuj artykuł
                </a>
                <!-- Meta Info -->
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center text-lg font-semibold">
                            {{ $post->author[0] }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $post->author }}</p>
                            <p class="text-sm text-gray-500">Opublikowano: {{ $post->created_at->format('d.m.Y') }}</p>
                        </div>
                    </div>
                    <div class="ml-auto flex gap-2">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                            {{ $post->slug }}
                        </span>
                        <span class="text-gray-500 text-sm">5 min czytania</span>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ $post->title }}
                </h1>

                <!-- Lead -->
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    {{ Str::limit($post->content, 200) }}
                </p>

                <!-- Content -->
                <div class="prose prose-lg max-w-none">
                    {{ $post->content }}
                </div>

                <!-- Tags -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-3">Tagi:</p>
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #laravel
                        </span>
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #php
                        </span>
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #docker
                        </span>
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 cursor-pointer">
                            #tutorial
                        </span>
                    </div>
                </div>

                <!-- Social Share -->
                <div class="mt-6 flex items-center gap-4">
                    <span class="text-sm text-gray-600">Udostępnij:</span>
                    <button class="text-blue-600 hover:text-blue-700">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </button>
                    <button class="text-sky-500 hover:text-sky-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <section class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                Komentarze (3)
            </h2>

            <!-- Comment Form -->
            <div class="mb-8 pb-8 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dodaj komentarz</h3>
                <form class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
                            Twoje imię *
                        </label>
                        <input type="text" id="author" name="author" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Jan Kowalski">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="jan@example.com">
                        <p class="mt-1 text-sm text-gray-500">Email nie będzie publikowany</p>
                    </div>

                    <!-- Comment Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Komentarz *
                        </label>
                        <textarea id="content" name="content" required rows="5"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                            placeholder="Podziel się swoimi przemyśleniami..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            Opublikuj komentarz
                        </button>
                        <p class="text-sm text-gray-500">* Pola wymagane</p>
                    </div>
                </form>
            </div>

            <!-- Comments List -->
            <div class="space-y-6">

                <!-- Comment 1 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                            AN
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">Anna Nowak</h4>
                                <span class="text-sm text-gray-500">2 godziny temu</span>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Świetny artykuł! Właśnie zaczynałam przygodę z Laravel i ten tutorial bardzo mi pomógł.
                                Sail to genialne rozwiązanie - wszystko działa od razu bez konfiguracji 🚀
                            </p>
                            <div class="mt-3 flex items-center gap-4">
                                <button class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                    Odpowiedz
                                </button>
                                <button class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                    <span>12</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comment 2 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                            MK
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">Marek Kowalczyk</h4>
                                <span class="text-sm text-gray-500">5 godzin temu</span>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Czy Laravel 11 ma jakieś breaking changes w porównaniu do wersji 10? Planujemy migrację
                                projektu i zastanawiam się, ile pracy nas czeka.
                            </p>
                            <div class="mt-3 flex items-center gap-4">
                                <button class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                    Odpowiedz
                                </button>
                                <button class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                    <span>5</span>
                                </button>
                            </div>
                        </div>

                        <!-- Nested Reply -->
                        <div class="ml-8 mt-4 flex gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    JK
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-semibold text-gray-900">Jan Kowalski</h4>
                                            <span
                                                class="px-2 py-0.5 bg-indigo-600 text-white text-xs rounded-full">Autor</span>
                                        </div>
                                        <span class="text-sm text-gray-500">3 godziny temu</span>
                                    </div>
                                    <p class="text-gray-700 leading-relaxed">
                                        @Marek - Breaking changes są minimalne! Głównie dotyczą struktury katalogów i
                                        konfiguracji. Polecam sprawdzić oficjalny upgrade guide na laravel.com
                                    </p>
                                    <div class="mt-3 flex items-center gap-4">
                                        <button
                                            class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                            </svg>
                                            <span>8</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comment 3 -->
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            KW
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">Kasia Wiśniewska</h4>
                                <span class="text-sm text-gray-500">1 dzień temu</span>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                Czy ktoś używał Sail w produkcji? Czy to tylko narzędzie developerskie, czy można na nim
                                oprzeć deployment?
                            </p>
                            <div class="mt-3 flex items-center gap-4">
                                <button class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                    Odpowiedz
                                </button>
                                <button class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                    <span>3</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Load More Comments -->
            <div class="mt-8 text-center">
                <button class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                    Załaduj więcej komentarzy
                </button>
            </div>
        </section>

        <!-- Related Posts -->
        <section class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Powiązane artykuły</h2>
            <div class="grid gap-6 md:grid-cols-3">

                <!-- Related Post 1 -->
                <a href="#" class="group">
                    <article
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div
                            class="h-32 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                            <span class="text-5xl">🤖</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 mb-2">
                                GitHub Copilot Agent Mode w praktyce
                            </h3>
                            <p class="text-sm text-gray-500">8 min czytania</p>
                        </div>
                    </article>
                </a>

                <!-- Related Post 2 -->
                <a href="#" class="group">
                    <article
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div class="h-32 bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                            <span class="text-5xl">⚛️</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 mb-2">
                                Inertia.js - most między Laravel a React
                            </h3>
                            <p class="text-sm text-gray-500">12 min czytania</p>
                        </div>
                    </article>
                </a>

                <!-- Related Post 3 -->
                <a href="#" class="group">
                    <article
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div
                            class="h-32 bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                            <span class="text-5xl">🎨</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 mb-2">
                                Laravel Filament - admin panel w 15 minut
                            </h3>
                            <p class="text-sm text-gray-500">6 min czytania</p>
                        </div>
                    </article>
                </a>

            </div>
        </section>

    </main>

</x-layout>
