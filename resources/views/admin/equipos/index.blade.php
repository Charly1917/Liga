@extends('admin.layouts.app')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-sm uppercase tracking-widest text-green-700 font-bold">Administración</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Equipos registrados</h1>
            <p class="text-slate-500 mt-1">Gestiona los equipos y sus escudos.</p>
        </div>
        <a href="{{ route('admin.equipos.create') }}" class="inline-flex justify-center bg-green-700 hover:bg-green-800 text-white px-5 py-3 rounded-xl font-bold shadow">
            + Agregar equipo
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-5">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($equipos as $equipo)
            <article class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                <x-equipo-escudo :equipo="$equipo" tamano="h-16 w-16" />
                <div class="min-w-0 flex-1">
                    <h2 class="font-extrabold text-slate-900 truncate">{{ $equipo->nombre }}</h2>
                    <p class="text-sm text-slate-500">Color del equipo</p>
                    <span class="inline-block mt-1 h-3 w-8 rounded-full border" style="background-color: {{ $equipo->color }}"></span>
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('admin.equipos.edit', $equipo) }}" class="text-sm font-bold text-blue-700 hover:underline">Editar</a>
                        <form action="{{ route('admin.equipos.destroy', $equipo) }}" method="POST" onsubmit="return confirm('¿Eliminar este equipo? Sus partidos relacionados también se eliminarán.')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm font-bold text-red-700 hover:underline">Eliminar</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="text-slate-500">Aún no hay equipos registrados.</p>
        @endforelse
    </div>
@endsection
