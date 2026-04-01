@extends('admin.layouts.admin')

@section('content')

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name" class="block font-medium text-gray-700">Nombre del Producto</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="sku" class="block font-medium text-gray-700">Número de Parte (SKU)</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="form-group">
                                <label for="brand" class="block font-medium text-gray-700">Marca</label>
                                <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="price" class="block font-medium text-gray-700">Precio</label>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="stock" class="block font-medium text-gray-700">Existencia (Stock)</label>
                                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
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
                            <textarea name="short_description" id="short_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded submit-btn">
                                Guardar Producto
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection