@extends('layouts.admin')

@section('title', 'Users')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Usuarios</h1>

    <a href="{{ route('admin.users.create') }}"
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
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->nickname }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->games_played }}</td>
                <td>{{ $user->games_won }}</td>
                <td>{{ $user->times_impostor }}</td>

                <td class="text-end">

                    <a href="{{ route('admin.users.show', $user) }}"
                       class="btn btn-sm btn-outline-secondary">
                        Ver
                    </a>

                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="btn btn-sm btn-outline-secondary">
                        Editar
                    </a>

                    <form action="{{ route('admin.users.destroy', $user) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('¿Eliminar usuario?')">
                            Eliminar
                        </button>
                    </form>

                </td>
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

{{ $users->links() }}

@endsection
