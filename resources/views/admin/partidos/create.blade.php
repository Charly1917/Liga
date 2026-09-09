@extends('admin.layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6">{{ $partido->id ?? '' ? 'Editar Partido' : 'Nuevo Partido' }}</h1>

<form action="{{ $partido->id ?? '' ? route('partidos.update', $partido->id) : route('partidos.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
    @csrf
    @if($partido->id ?? false)
        @method('PUT')
    @endif

    <div>
        <label class="block mb-1 font-semibold">Equipo Local</label>
        <select name="equipo_local_id" class="w-full border p-2 rounded">
            <option value="">Selecciona un equipo</option>
            @foreach($equipos as $equipo)
                <option value="{{ $equipo->id }}" @if(($partido->equipo_local_id ?? '') == $equipo->id) selected @endif>{{ $equipo->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1 font-semibold">Equipo Visitante</label>
        <select name="equipo_visitante_id" class="w-full border p-2 rounded">
            <option value="">Selecciona un equipo</option>
            @foreach($equipos as $equipo)
                <option value="{{ $equipo->id }}" @if(($partido->equipo_visitante_id ?? '') == $equipo->id) selected @endif>{{ $equipo->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1 font-semibold">Fecha y Hora</label>
        <input type="datetime-local" name="fecha" value="{{ $partido->fecha ?? '' }}" class="w-full border p-2 rounded">
    </div>

    <div class="flex gap-4">
        <div>
            <label class="block mb-1 font-semibold">Goles Local</label>
            <input type="number" name="goles_local" value="{{ $partido->goles_local ?? '' }}" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block mb-1 font-semibold">Goles Visitante</label>
            <input type="number" name="goles_visitante" value="{{ $partido->goles_visitante ?? '' }}" class="w-full border p-2 rounded">
        </div>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
        {{ $partido->id ?? '' ? 'Actualizar' : 'Guardar' }}
    </button>
</form>
@endsection
