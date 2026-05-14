@extends('layouts.admin')

@section('title', 'Detalle de usuario')

@section('content')

<h1>Detalle de usuario</h1>

<p>
    <strong>ID:</strong> {{ $user->id }}
</p>

<p>
    <strong>Nombre:</strong> {{ $user->name }}
</p>

<p>
    <strong>Nickname:</strong> {{ $user->nickname }}
</p>

<p>
    <strong>Email:</strong> {{ $user->email }}
</p>

<p>
    <strong>Partidas jugadas:</strong> {{ $user->games_played }}
</p>

<p>
    <strong>Partidas ganadas:</strong> {{ $user->games_won }}
</p>

<p>
    <strong>Veces impostor:</strong> {{ $user->times_impostor }}
</p>

<p>
    <strong>Rol:</strong> {{ $user->role_user }}
</p>

<hr>

<a href="{{ route('admin.users.index') }}">
    Volver al listado
</a>

<a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary ms-3">
    Editar usuario
</a>

@endsection