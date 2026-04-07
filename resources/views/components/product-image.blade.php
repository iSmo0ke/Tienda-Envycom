@props(['image', 'alt' => 'Imagen del producto', 'cssClass' => ''])

@if($image)
    @if(\Illuminate\Support\Str::startsWith($image, ['http://', 'https://']))
        <img src="{{ $image }}" alt="{{ $alt }}" class="{{ $cssClass }}">
    @else
        <img src="{{ asset('storage/' . $image) }}" alt="{{ $alt }}" class="{{ $cssClass }}">
    @endif
@else
    <div src="asset('img/icono-gris.jpg');" class="{{ $cssClass }} d-flex align-items-center justify-content-center text-muted" style="background-color: #f8f9fa;">
        <span>Sin Img</span>
    </div>
@endif