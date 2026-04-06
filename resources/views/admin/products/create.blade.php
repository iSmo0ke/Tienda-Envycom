@extends('admin.layouts.admin')

@section('content')

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        <div class="form-group">
                            <label for="name" class="block font-medium text-gray-700">Nombre del Producto</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label for="sku" class="block font-medium text-gray-700">Número de Parte (SKU)</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="form-group">
                                <label for="modelo" class="block font-medium text-gray-700">Modelo</label>
                                <input type="text" name="model" id="modelo" value="{{ old('modelo') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="form-group">
                                <label for="brand" class="block font-medium text-gray-700">Marca</label>
                                <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="category" class="block font-medium text-gray-700">Categoría</label>
                                <input list="category-options" name="category" id="category" value="{{ old('category') }}" 
                                    placeholder="Escribe o selecciona una..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                
                                <datalist id="category-options">
                                    @if(isset($categories))
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}">
                                        @endforeach
                                    @endif
                                </datalist>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="price" class="block font-medium text-gray-700">Precio</label>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="stock" class="block font-medium text-gray-700">Existencia (Stock)</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="form-group p-4 border border-gray-200 rounded-md bg-gray-50">
                            <label class="block font-medium text-gray-700 mb-2">Especificaciones Técnicas</label>

                            <div id="specs-container" class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="spec_labels[]" placeholder="Ej. Procesador"
                                        class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <input type="text" name="spec_values[]" placeholder="Ej. Intel Core Ultra 7"
                                        class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" class="text-red-500 hover:text-red-700 font-bold px-2 remove-spec"
                                        title="Eliminar fila">&times;</button>
                                </div>
                            </div>

                            <button type="button" id="add-spec"
                                class="mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center">
                                + Añadir otra especificación
                            </button>
                        </div>

                        <div class="form-group">
                            <label for="image" class="block font-medium text-gray-700">Imagen del Producto</label>
                            <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100">
                            @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="short_description" class="block font-medium text-gray-700">Descripción Corta</label>
                            <textarea name="short_description" id="short_description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="{{ route('admin.products.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded submit-btn">
                                Guardar Producto
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('specs-container');
                const btnAdd = document.getElementById('add-spec');

                // Añadir nueva fila
                btnAdd.addEventListener('click', function () {
                    const row = document.createElement('div');
                    row.className = 'flex items-center space-x-2 mt-2';
                    row.innerHTML = `
                                        <input type="text" name="spec_labels[]" placeholder="Ej. Memoria RAM" class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <input type="text" name="spec_values[]" placeholder="Ej. 16 GB DDR5" class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" class="text-red-500 hover:text-red-700 font-bold px-2 remove-spec" title="Eliminar fila">&times;</button>
                                    `;
                    container.appendChild(row);
                });

                // Eliminar fila (delegación de eventos)
                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-spec')) {
                        // Evitar que borren la última fila si solo queda una (opcional, pero buena práctica)
                        if (container.children.length > 1) {
                            e.target.parentElement.remove();
                        } else {
                            // Si es la última, solo limpiamos los inputs
                            const inputs = e.target.parentElement.querySelectorAll('input');
                            inputs.forEach(input => input.value = '');
                        }
                    }
                });
            });
        </script>
    </div>
@endsection