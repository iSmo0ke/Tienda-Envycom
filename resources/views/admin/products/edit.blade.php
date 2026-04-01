@extends('admin.layouts.admin')

@section('content')

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div>
                        <x-input-label for="nombre" value="Nombre del Producto" />
                        <x-text-input name="nombre" type="text" class="block w-full"
                            :value="old('nombre', $product->nombre)" required />
                        <x-input-error :messages="$errors->get('nombre')" />
                    </div>

                    <!-- SKU y Marca -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="numParte" value="Número de Parte (SKU)" />
                            <x-text-input name="numParte" type="text" class="block w-full"
                                :value="old('numParte', $product->numParte)" />
                        </div>

                        <div>
                            <x-input-label for="marca" value="Marca" />
                            <x-text-input name="marca" type="text" class="block w-full"
                                :value="old('marca', $product->marca)" />
                        </div>
                    </div>

                    <!-- Precio y Stock -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="precio" value="Precio (MXN)" />
                            <x-text-input name="precio" type="number" step="0.01" class="block w-full"
                                :value="old('precio', $product->precio)" required />
                            <x-input-error :messages="$errors->get('precio')" />
                        </div>

                        <div>
                            <x-input-label for="stock" value="Existencia (Stock)" />
                            <x-text-input name="stock" type="number" class="block w-full"
                                :value="old('stock', $product->existencia['local'] ?? 0)" required />
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="grid grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">Imagen Actual</label>

                            @if($product->imagen)
                            @if(\Illuminate\Support\Str::startsWith($product->imagen, ['http://', 'https://']))
                            <img src="{{ $product->imagen }}" class="h-32 object-contain border rounded p-1">
                            @else
                            <img src="{{ asset('storage/' . $product->imagen) }}" class="h-32 object-contain border rounded p-1">
                            @endif
                            @else
                            <div class="h-32 w-32 bg-gray-100 border border-dashed flex items-center justify-center text-gray-400 text-sm rounded">
                                Sin imagen
                            </div>
                            @endif
                        </div>

                        <div class="col-span-2">
                            <x-input-label for="image" value="Cambiar Imagen (Opcional)" />
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1">Si no seleccionas nada, se mantiene la imagen.</p>
                            <x-input-error :messages="$errors->get('image')" />
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <x-input-label for="descripcion_corta" value="Descripción Corta" />
                        <textarea name="descripcion_corta" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('descripcion_corta', $product->descripcion_corta) }}</textarea>
                    </div>

                    <!-- Activo -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="activo" value="1"
                                {{ old('activo', $product->activo) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-600">Mostrar en tienda</span>
                        </label>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-between items-center pt-4 border-t">
                        <a href="{{ route('admin.products.index') }}" class="text-gray-600">
                            Cancelar
                        </a>

                        <x-primary-button>
                            Actualizar Producto
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection