@props(['image', 'alt' => '', 'cssClass' => ''])

@php
    // Lógica para determinar el origen de la imagen
    $src = $image;

    // Si no es una URL (no empieza con http), buscamos en el storage local
    if ($image && !str_starts_with($image, 'http')) {
        $src = asset('storage/' . $image);
    }

    // Imagen por defecto si no existe
    if (!$image) {
        $src = asset('img/icono-gris.jpg');
    }
@endphp

<img src="{{ $src }}" alt="{{ $alt }}" class="{{ $cssClass }}" loading="lazy">