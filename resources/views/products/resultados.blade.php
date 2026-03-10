<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-3">Resultados de búsqueda</h1>

        @if($search)
            <p>Buscando: <strong>{{ $search }}</strong></p>
        @endif

        <div class="row">
            @forelse($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $product->imagen }}" class="card-img-top" alt="{{ $product->nombre }}">
                        <div class="card-body">
                            <h5>{{ $product->nombre }}</h5>
                            <p>{{ $product->descripcion }}</p>
                            <p><strong>${{ number_format($product->precio, 2) }}</strong></p>
                        </div>
                    </div>
                </div>
            @empty
                <p>No se encontraron productos.</p>
            @endforelse
        </div>
    </div>
</body>
</html>
