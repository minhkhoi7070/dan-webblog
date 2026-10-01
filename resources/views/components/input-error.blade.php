@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'invalid-feedback d-block mt-1 small']) }}>
        @foreach ((array) $messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif
