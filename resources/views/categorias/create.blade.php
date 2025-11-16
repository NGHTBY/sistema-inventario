@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6 w-1/2">

    <h1 class="text-xl font-bold mb-4">Crear Categoría</h1>

    <form action="{{ route('categorias.store') }}" method="POST">
        @csrf

        <label class="block mb-2">Nombre:</label>
        <input type="text" name="nombre" class="w-full border p-2 rounded mb-4">

        <label class="block mb-2">Descripción:</label>
        <textarea name="descripcion" class="w-full border p-2 rounded mb-4"></textarea>

        <button class="bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
        <a href="{{ route('categorias.index') }}" class="ml-3 text-gray-600">Cancelar</a>
    </form>

</div>
@endsection
