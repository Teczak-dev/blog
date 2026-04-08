@component('mail::message')
# Nowa wiadomość prywatna! 💬

Cześć **{{ $recipient->name }}**!

Otrzymałeś/aś nową wiadomość od **{{ $message->user->name }}**:

@component('mail::panel')
{{ $message->content }}
@endcomponent

@component('mail::button', ['url' => $conversationUrl])
Odpowiedz na wiadomość
@endcomponent

**Wysłano:** {{ $message->created_at->format('d.m.Y H:i') }}

---

Możesz zarządzać swoimi powiadomieniami w ustawieniach profilu.

Pozdrawiam,  
{{ config('app.name') }}
@endcomponent