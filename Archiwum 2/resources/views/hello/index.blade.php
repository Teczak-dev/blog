<div>
    <h1>Hello world {{ $name }}</h1>

    {!! $html !!}

    @if (true)
        <p>tak</p>
    @else
        <p>nie</p>
    @endif

    <ul>
        @foreach ($items as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>

    <ul>
        @forelse($items as $item)
            <li>{{ $item }}</li>
        @empty
            <li>brak elementów</li>
        @endforelse
    </ul>
</div>
