<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Obserwujący {{ $user->name }}
                    </h1>
                    <p class="text-gray-600 mt-1">{{ $followers->total() }} obserwujących</p>
                </div>
                
                <div class="p-6">
                    @if($followers->count() > 0)
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($followers as $follower)
                                <div class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg">
                                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">
                                            <a href="{{ route('users.profile', $follower) }}" class="hover:text-blue-600">
                                                {{ $follower->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ $follower->email }}</p>
                                        <p class="text-xs text-gray-400">
                                            Obserwuje od {{ $follower->pivot->created_at->format('d.m.Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($followers->hasPages())
                            <div class="mt-6">
                                {{ $followers->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <p class="text-gray-500">{{ $user->name }} nie ma jeszcze obserwujących.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>