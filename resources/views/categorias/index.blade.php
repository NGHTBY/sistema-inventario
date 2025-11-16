@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6">

    <div class="flex justify-between mb-4">
        <h1 class="text-2xl font-bold">Categorías</h1>
        <a href="{{ route('categorias.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nueva categoría</a>
    </div>

    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="p-2">Nombre</th>
                <th class="p-2">Descripción</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categorias as $c)
                <tr class="border-b">
                    <td class="p-2">{{ $c->nombre }}</td>
                    <td class="p-2">{{ $c->descripcion }}</td>
                    <td class="p-2 flex gap-3">
                        <a href="{{ route('categorias.edit', $c) }}" class="text-blue-600">Editar</a>
                        <form action="{{ route('categorias.destroy', $c) }}" method="POST">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('¿Seguro?')" class="text-red-600">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
