@extends('layouts.admin')

@section('title', 'Login')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <h3 class="card-title mb-4 text-center">
                    Iniciar sesión
                </h3>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email
                        </label>
                        <input 
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Contraseña
                        </label>
                        <input 
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            id="password"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Entrar
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
