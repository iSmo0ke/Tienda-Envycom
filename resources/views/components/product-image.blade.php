@props(['image', 'alt', 'cssClass' => ''])

@if($image)
    @if(\Illuminate\Support\Str::startsWith($image, ['http://', 'https://']))
        <img src="{{ $image }}" alt="{{ $alt }}" class="{{ $cssClass }}">
    @else
        <img src="{{ asset('storage/' . $image) }}" alt="{{ $alt }}" class="{{ $cssClass }}">
    @endif
@else
    <img src="{{ asset('img/logo.png') }}" alt="Sin imagen" class="{{ $cssClass }} object-contain bg-gray-100 p-4">
@endif