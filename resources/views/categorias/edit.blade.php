@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6 w-1/2">

    <h1 class="text-xl font-bold mb-4">Editar Categoría</h1>

    <form action="{{ route('categorias.update', $categoria) }}" method="POST">
        @csrf @method('PUT')

        <label class="block mb-2">Nombre:</label>
        <input type="text" name="nombre" value="{{ $categoria->nombre }}" class="w-full border p-2 rounded mb-4">

        <label class="block mb-2">Descripción:</label>
        <textarea name="descripcion" class="w-full border p-2 rounded mb-4">{{ $categoria->descripcion }}</textarea>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
        <a href="{{ route('categorias.index') }}" class="ml-3 text-gray-600">Cancelar</a>
    </form>

</div>
@endsection
