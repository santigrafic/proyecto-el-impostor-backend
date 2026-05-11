<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text"
           name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $user->name ?? '') }}">

    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Nickname</label>
    <input type="text"
           name="nickname"
           class="form-control @error('nickname') is-invalid @enderror"
           value="{{ old('nickname', $user->nickname ?? '') }}">

    @error('nickname')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email"
           name="email"
           class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $user->email ?? '') }}">

    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Contraseña</label>
    <input type="password"
           name="password"
           class="form-control @error('password') is-invalid @enderror">

    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if(isset($user))
        <small class="text-muted">
            Déjalo vacío si no quieres cambiar la contraseña
        </small>
    @endif
</div>