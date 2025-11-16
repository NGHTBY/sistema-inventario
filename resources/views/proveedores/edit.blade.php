@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Editar Proveedor</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="empresa" class="block text-gray-700 font-semibold">Empresa *</label>
            <input type="text" name="empresa" id="empresa" value="{{ old('empresa', $proveedor->empresa) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div class="mb-4">
            <label for="contacto" class="block text-gray-700 font-semibold">Contacto *</label>
            <input type="text" name="contacto" id="contacto" value="{{ old('contacto', $proveedor->contacto) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-semibold">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $proveedor->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="telefono" class="block text-gray-700 font-semibold">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $proveedor->telefono) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="direccion" class="block text-gray-700 font-semibold">Dirección</label>
            <textarea name="direccion" id="direccion" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('direccion', $proveedor->direccion) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="nit" class="block text-gray-700 font-semibold">NIT</label>
            <input type="text" name="nit" id="nit" value="{{ old('nit', $proveedor->nit) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('proveedores.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Actualizar Proveedor</button>
        </div>
    </form>
</div>
@endsection