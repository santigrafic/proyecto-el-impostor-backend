@extends('layouts.admin')

@section('title', 'Partidas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Partidas</h1>
</div>

<table class="table table-bordered table-hover bg-white">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Tema</th>
            <th>Palabra</th>
            <th>Impostor</th>
            <th>Ganador</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>

    <tbody>
        @forelse($games as $game)
            @php
                $impostor = $game->users->firstWhere('pivot.role', 'impostor');
            @endphp

            <tr>
                <td>{{ $game->id }}</td>

                <td>{{ $game->theme }}</td>

                <td>{{ $game->word }}</td>

                {{-- IMPOSTOR --}}
                <td>
                    {{ $impostor->name ?? 'N/A' }}
                </td>

                {{-- GANADOR (campo directo en games) --}}
                <td>
                    {{ $game->winner ?? 'N/A' }}
                </td>

                <td>
                    {{ $game->started_at }}
                </td>

                <td>
                    {{ $game->finished_at }}
                </td>

                <td class="text-end">
                    <a href="{{ route('admin.games.show', $game) }}"
                       class="btn btn-sm btn-primary">
                        Ver
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted">
                    No hay partidas registradas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $games->links() }}
</div>

@endsection