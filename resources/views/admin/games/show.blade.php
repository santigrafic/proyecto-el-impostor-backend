@extends('layouts.admin')

@section('title', 'Detalle de partida')

@section('content')

<h1>Detalle de partida</h1>

<p>
    <strong>ID:</strong> {{ $game->id }}
</p>

<p>
    <strong>Tema:</strong> {{ $game->theme }}
</p>

<p>
    <strong>Palabra:</strong> {{ $game->word }}
</p>

<p>
    <strong>Impostor:</strong>
    @php
        $impostor = $game->users->firstWhere('pivot.role', 'impostor');
    @endphp

    {{ $impostor->name ?? 'N/A' }}
</p>

<p>
    <strong>Ganador:</strong> {{ $game->winner ?? 'N/A' }}
</p>

<p>
    <strong>Inicio:</strong> {{ $game->started_at }}
</p>

<p>
    <strong>Fin:</strong> {{ $game->finished_at }}
</p>

<hr>

<a href="{{ route('admin.games.index') }}">
    Volver al listado
</a>

@endsection