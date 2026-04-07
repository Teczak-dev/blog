<x-layout>
    <form method="POST" action="{{ route('posts.update', $post->slug) }}" enctype="multipart/form-data" class="flex flex-col max-w-3xl mx-auto my-4">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <ul class="bg-red-200 text-red-700 p-6 mb-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <label>Tytul</label>
        <input type="text" name="title" value="{{ old('title', $post->title) }}" />
        @error('title')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Przyjazny adres</label>
        <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" />
        @error('slug')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Autor</label>
        <input type="text" name="author" value="{{ old('author', $post->author) }}" />
        @error('author')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Zajawka</label>
        <textarea name="lead">{{ old('lead', $post->lead) }}</textarea>
        @error('lead')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        @if ($post->photo)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Aktualne zdjęcie:</label>
                <img src="{{ asset('storage/' . $post->photo) }}" alt="{{ $post->title }}" class="w-32 h-32 object-cover rounded-lg mt-2">
            </div>
        @endif

        <label>Zdjęcie {{ $post->photo ? '(zostaw puste aby zachować obecne)' : '' }}</label>
        <input type="file" name="photo" accept="image/*" />
        @error('photo')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Treść</label>
        <textarea name="content">{{ old('content', $post->content) }}</textarea>
        @error('content')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <button type="submit" class="bg-blue-700 text-white p-4 mt-4">Zaktualizuj</button>
    </form>
</x-layout>