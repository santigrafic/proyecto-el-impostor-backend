@extends('layouts.admin')

@section('title', 'Editar usuario')

@section('content')

<h1 class="h3 mb-4">Editar usuario</h1>

<form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    @include('admin.users._form')

    <div class="mt-3">
        <button class="btn btn-primary">Actualizar</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
    </div>
</form>

@endsection
