@extends('admin.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
<a href="{{ route('admin.equipos.index') }}" class="inline-flex mb-5 text-slate-500 hover:text-slate-900 font-semibold">← Volver a equipos</a>
<h1 class="text-3xl font-extrabold text-slate-900 mb-6">{{ $equipo->exists ? 'Editar equipo' : 'Agregar equipo' }}</h1>

<form action="{{ $equipo->exists ? route('admin.equipos.update', $equipo) : route('admin.equipos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 md:p-8 space-y-5">
    @csrf
    @if($equipo->exists)
        @method('PUT')
    @endif

    <div>
        <label for="nombre" class="block mb-2 font-bold text-slate-700">Nombre del equipo</label>
        <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $equipo->nombre) }}" required maxlength="255" class="w-full border-slate-300 p-3 rounded-xl focus:border-green-600 focus:ring-green-600">
        @error('nombre')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="color" class="block mb-2 font-bold text-slate-700">Color representativo</label>
        <input id="color" type="color" name="color" value="{{ old('color', $equipo->color ?: '#0a1a44') }}" class="h-12 w-20 border border-slate-300 rounded-lg">
    </div>

    <div>
        <label for="logo" class="block mb-2 font-bold text-slate-700">Escudo (opcional)</label>
        <input id="logo" type="file" name="logo" accept="image/*" class="w-full rounded-xl border border-slate-300 p-3">
        <p class="text-sm text-slate-500 mt-1">PNG, JPG o WEBP, máximo 2 MB. Si no subes uno, se mostrará una insignia con las iniciales.</p>
        @error('logo')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        @if($equipo->logo)
            <div class="mt-4"><x-equipo-escudo :equipo="$equipo" tamano="h-20 w-20" /></div>
        @endif
    </div>

    <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white py-3 px-5 rounded-xl font-extrabold shadow">
        {{ $equipo->exists ? 'Guardar cambios' : 'Agregar equipo' }}
    </button>
</form>
</div>
@endsection
