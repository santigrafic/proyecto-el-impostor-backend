@extends('layouts.admin')

@section('title', 'Users')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Usuarios</h1>

    <a href="{{ route('admin.peliculas.create') }}"
       class="btn btn-primary">
        Nuevo Usuario
    </a>
</div>

<table class="table table-bordered table-hover bg-white">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nickname</th>
            <th>email</th>
            <th>Jugadas</th>
            <th>Ganadas</th>
            <th>Impostor</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
            <tr>
                
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No hay usuarios registrados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $usuarios->links() }}

@endsection
