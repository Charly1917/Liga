@props(['equipo', 'tamano' => 'h-12 w-12'])

@php
    $nombreArchivo = $equipo->logo ? basename($equipo->logo) : null;
    $rutaArchivo = $nombreArchivo ? public_path('img/equipos/' . $nombreArchivo) : null;
    $iniciales = collect(explode(' ', trim($equipo->nombre)))
        ->filter()
        ->take(2)
        ->map(fn ($palabra) => mb_strtoupper(mb_substr($palabra, 0, 1)))
        ->implode('');
@endphp

@if($rutaArchivo && is_file($rutaArchivo))
    <img src="{{ asset('img/equipos/' . $nombreArchivo) }}"
         class="{{ $tamano }} object-contain"
         alt="Escudo de {{ $equipo->nombre }}">
@else
    <span class="{{ $tamano }} inline-flex shrink-0 items-center justify-center rounded-full text-white font-extrabold shadow-sm"
          style="background-color: {{ $equipo->color ?: '#0a1a44' }}"
          role="img"
          aria-label="Insignia de {{ $equipo->nombre }}">
        {{ $iniciales ?: 'EQ' }}
    </span>
@endif
