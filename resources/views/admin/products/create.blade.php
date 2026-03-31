@extends('admin.layouts.admin')

@section('content')

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">

                <h2 class="text-xl font-bold mb-4">Crear Producto</h2>

                <form action="{{ route('admin.products.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <x-input-label for="nombre" value="Nombre del Producto" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required />
                            <x-input-error :messages="$errors->get('nombre')" />
                        </div>

                        <div>
                            <x-input-label for="sku" value="SKU" />
                            <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku')" required />
                            <x-input-error :messages="$errors->get('sku')" />
                        </div>

                        <div>
                            <x-input-label for="precio" value="Precio (MXN)" />
                            <x-text-input id="precio" name="precio" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('precio')" required />
                            <x-input-error :messages="$errors->get('precio')" />
                        </div>

                        <div>
                            <x-input-label for="marca" value="Marca" />
                            <x-text-input id="marca" name="marca" type="text" class="mt-1 block w-full" :value="old('marca')" required />
                            <x-input-error :messages="$errors->get('marca')" />
                        </div>

                    </div>

                    {{-- CHECKBOX --}}
                    <div class="mt-4">
                        <input type="hidden" name="activo" value="0">

                        <label class="inline-flex items-center">
                            <input type="checkbox" name="activo" value="1"
                                {{ old('activo') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="ml-2 text-sm text-gray-600">
                                Producto Activo
                            </span>
                        </label>
                    </div>

                    {{-- BOTÓN --}}
                    <div class="mt-6 flex justify-end">
                        <x-primary-button>
                            Guardar Producto
                        </x-primary-button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection