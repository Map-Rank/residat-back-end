@component('mail::message')
# Voici ce que vous avez raté

@foreach ($posts as $post)
- **{{ $post->content }}** : {{ $post->created_at->format('d/m/Y H:i') }}
@endforeach

Merci,
{{ config('app.name') }}
@endcomponent