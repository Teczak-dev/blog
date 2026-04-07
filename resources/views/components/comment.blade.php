<div class="flex gap-4 mb-6">
    <div class="flex-shrink-0">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
            {{ strtoupper(substr($comment->author_display_name, 0, 2)) }}
        </div>
    </div>
    <div class="flex-1">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <h4 class="font-semibold text-gray-900">{{ $comment->author_display_name }}</h4>
                    @if($comment->isFromLoggedUser())
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Użytkownik</span>
                    @endif
                </div>
                <span class="text-sm text-gray-500">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
            </div>
            <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $comment->content }}</div>
        </div>
    </div>
</div>