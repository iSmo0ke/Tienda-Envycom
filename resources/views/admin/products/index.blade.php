@extends('admin.layouts.admin')

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium">Lista de Productos</h3>
                    <a href="{{ route('admin.products.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        + Añadir Producto
                    </a>
                </div>

                <!-- Mensaje éxito -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU / Marca</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Origen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($products as $product)
                                <tr class="hover:bg-gray-50">

                                    <!-- Nombre -->
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $product->nombre }}
                                    </td>

                                    <!-- SKU y Marca -->
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $product->numParte ?? 'N/A' }} <br>
                                        <span class="text-xs text-gray-400">{{ $product->marca }}</span>
                                    </td>

                                    <!-- Precio -->
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        ${{ number_format($product->precio, 2) }}
                                    </td>

                                    <!-- Origen -->
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs font-semibold rounded-full 
                                            {{ $product->source == 'CT' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $product->source ?? 'LOCAL' }}
                                        </span>
                                    </td>

                                    <!-- Estado -->
                                    <td class="px-6 py-4">
                                        @if($product->activo)
                                            <span class="text-green-600 font-bold">Activo</span>
                                        @else
                                            <span class="text-red-400">Inactivo</span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Editar
                                        </a>

                                        <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>
</div>

@endsection