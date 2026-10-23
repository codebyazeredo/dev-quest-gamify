<x-layouts.guest :title="__('Entrar')">
    <h1 class="h4 mb-1">Faça seu login</h1>
    <p class="text-body-secondary mb-4">Entre para continuar suas missões.</p>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="vstack gap-3">
        @csrf

        <div>
            <label for="email" class="form-label fw-medium small mb-1">E-mail</label>
            <div class="input-group">
                <span class="input-group-text"><x-icon name="mail" /></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username" placeholder="seu@email.com">
            </div>
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="form-label fw-medium small mb-1">Senha</label>
            <div class="input-group">
                <span class="input-group-text"><x-icon name="lock" /></span>
                <input :type="show ? 'text' : 'password'" id="password" name="password" class="form-control" required autocomplete="current-password" placeholder="sua senha">
                <button type="button" @click="show = !show" title="Mostrar/ocultar senha" aria-label="Mostrar/ocultar senha" class="btn btn-outline-secondary">
                    <span x-show="!show"><x-icon name="eye" /></span>
                    <span x-show="show" x-cloak><x-icon name="eye-off" /></span>
                </button>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between small">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Lembrar de mim</label>
            </div>

            <a href="{{ route('password.request') }}">Esqueceu sua senha?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
</x-layouts.guest>
