@extends('admin.layouts.app')

@section('content')
 <a href="{{ route('admin') }}"
   class="inline-block mb-5 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
    ← Regresar
</a>

<h1 class="text-3xl font-bold mb-6">Equipos</h1>

<a href="{{ route('equipos.create') }}" class="mb-4 inline-block bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">Nuevo Equipo</a>

<table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-2 text-left">Logo</th>
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Color</th>
            <th class="p-2 text-left">Entrenador</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($equipos as $equipo)
        <tr class="border-b">
            <td class="p-2"><img src="{{ asset('img/equipos/' . $equipo->logo) }}" class="w-10 h-10" alt=""></td>
            <td class="p-2">{{ $equipo->nombre }}</td>
            <td class="p-2">{{ $equipo->color }}</td>
            <td class="p-2">{{ $equipo->entrenador }}</td>
            <td class="p-2 flex gap-2">
                <a href="{{ route('equipos.edit', $equipo->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded">Editar</a>
                <form action="{{ route('equipos.destroy', $equipo->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white py-1 px-2 rounded" onclick="return confirm('¿Eliminar equipo?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
