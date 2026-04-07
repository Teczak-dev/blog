<x-layout>
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="flex flex-col max-w-3xl mx-auto my-4">
        @csrf

        @if ($errors->any())
            <ul class="bg-red-200 text-red-700 p-6 mb-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <label>Tytul</label>
        <input type="text" name="title" value="{{ old('title') }}" />
        @error('title')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Przyjazny adres</label>
        <input type="text" name="slug" value="{{ old('slug') }}" />
        @error('slug')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Autor</label>
        <input type="text" name="author" value="{{ old('author') }}" />
        @error('author')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Zajawka</label>
        <textarea name="lead">{{ old('lead') }}</textarea>
        @error('lead')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Zdjęcie</label>
        <input type="file" name="photo" accept="image/*" />
        @error('photo')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <label>Treść</label>
        <textarea name="content">{{ old('content') }}</textarea>
        @error('content')
            <div class="text-red-500">{{ $message }}</div>
        @enderror

        <button type="submit" class="bg-blue-700 text-white p-4 mt-4">Dodaj</button>
    </form>
</x-layout>
