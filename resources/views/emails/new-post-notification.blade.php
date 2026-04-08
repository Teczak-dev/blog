@component('mail::message')
# Nowy post od obserwowanego! 📝

Cześć **{{ $follower->name }}**!

Użytkownik **{{ $post->user->name }}**, którego obserwujesz, właśnie opublikował nowy post:

## {{ $post->title }}

@if($post->lead)
{{ $post->lead }}
@endif

@component('mail::button', ['url' => $postUrl])
Przeczytaj post
@endcomponent

**Kategoria:** {{ $post->category }}  
**Czas czytania:** {{ $post->read_time_minutes }} min

---

Możesz zarządzać swoimi powiadomieniami w ustawieniach profilu.

Pozdrawiam,  
{{ config('app.name') }}
@endcomponent