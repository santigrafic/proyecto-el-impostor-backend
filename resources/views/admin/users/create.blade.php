@extends('layouts.admin')

@section('title', 'Nuevo usuario')

@section('content')

<h1 class="h3 mb-4">Nuevo usuario</h1>

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    @include('admin.users._form')

    <div class="mt-3">
        <button class="btn btn-primary">Guardar</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
    </div>
</form>

@endsection