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
                    <li class="nav-item">
                        <span class="nav-link">
                            {{ auth()->user()->name }}
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
