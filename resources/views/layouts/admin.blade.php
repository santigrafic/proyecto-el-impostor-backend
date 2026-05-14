<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'EL IMPOSTOR')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="{{ route('admin.users.index') }}">
            EL IMPOSTOR
        </a>

        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">

                @auth
                {{-- USERS --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            Usuarios
                        </a>
                    </li>

                    {{-- GAMES --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.games.index') }}">
                            Partidas
                        </a>
                    </li>
                    {{-- LOGOUT --}}
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="nav-link"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </a>
                        </form>
                    </li>

                    <li class="nav-item">
                        <span class="nav-link text-white" style="cursor: default;">
                           Hola, {{ auth()->user()->name }}
                        </span>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            Login
                        </a>
                    </li>
                @endguest

            </ul>
        </div>

    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

</body>
</html>
