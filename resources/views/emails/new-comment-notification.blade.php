<x-mail::message>
# Nowy komentarz pod Twoim postem! 💬

Witaj {{ $postAuthor->name }}!

Ktoś właśnie skomentował Twój post **"{{ $post->title }}"**.

## Szczegóły komentarza:

**Autor:** {{ $comment->author_name ?: 'Anonim' }}  
**Data:** {{ $comment->created_at->format('d.m.Y H:i') }}

**Treść komentarza:**
> {{ $comment->content }}

<x-mail::button :url="$url">
Zobacz komentarz
</x-mail::button>

## Zarządzanie powiadomieniami

Jeśli nie chcesz otrzymywać takich powiadomień, możesz je wyłączyć w ustawieniach swojego profilu.

<x-mail::button :url="url('/profile')" color="secondary">
Przejdź do ustawień
</x-mail::button>

Pozdrawiamy,<br>
{{ config('app.name') }}

<x-slot:subcopy>
Jeśli masz problemy z kliknięciem przycisku "Zobacz komentarz", skopiuj i wklej poniższy adres do przeglądarki:
[{{ $url }}]({{ $url }})
</x-slot:subcopy>
</x-mail::message>