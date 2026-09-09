@extends('admin.layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6">{{ $equipo->id ?? '' ? 'Editar Equipo' : 'Nuevo Equipo' }}</h1>

<form action="{{ $equipo->id ?? '' ? route('equipos.update', $equipo->id) : route('equipos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6 space-y-4">
    @csrf
    @if($equipo->id ?? false)
        @method('PUT')
    @endif

    <div>
        <label class="block mb-1 font-semibold">Nombre</label>
        <input type="text" name="nombre" value="{{ $equipo->nombre ?? '' }}" class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block mb-1 font-semibold">Color</label>
        <input type="color" name="color" value="{{ $equipo->color ?? '#ffffff' }}" class="w-16 h-10 border rounded">
    </div>

    <div>
        <label class="block mb-1 font-semibold">Entrenador</label>
        <input type="text" name="entrenador" value="{{ $equipo->entrenador ?? '' }}" class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block mb-1 font-semibold">Logo</label>
        <input type="file" name="logo" class="w-full">
        @if(isset($equipo->logo))
            <img src="{{ asset('img/equipos/' . $equipo->logo) }}" class="w-20 h-20 mt-2" alt="">
        @endif
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
        {{ $equipo->id ?? '' ? 'Actualizar' : 'Guardar' }}
    </button>
</form>
@endsection
