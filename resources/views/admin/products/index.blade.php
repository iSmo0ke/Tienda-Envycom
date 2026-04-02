@extends('admin.layouts.admin')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Lista de Productos</h3>
                        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Añadir Producto Local
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4 border-b text-left">ID / SKU</th>
                                <th class="py-2 px-4 border-b text-left">Nombre</th>
                                <th class="py-2 px-4 border-b text-left">Marca</th>
                                <th class="py-2 px-4 border-b text-left">Precio</th>
                                <th class="py-2 px-4 border-b text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">{{ $product->numParte ?? 'N/A' }}</td>
                                    <td class="py-2 px-4 border-b">{{ $product->nombre }}</td>
                                    <td class="py-2 px-4 border-b">{{ $product->marca }}</td>
                                    <td class="py-2 px-4 border-b">${{ number_format($product->precio, 2) }}</td>
                                    <td class="py-2 px-4 border-b text-center space-x-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-500 hover:text-blue-700">Editar</a>
                                        
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection