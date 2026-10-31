<x-layouts.auth :title="__('Esqueci minha senha')">
    <h1 class="auth-title">Esqueceu sua senha?</h1>
    <p class="auth-lead">Informe seu e-mail e enviaremos um link para redefinir sua senha.</p>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="vstack gap-3">
        @csrf

        <div>
            <label for="email" class="form-label fw-medium small mb-1">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100 auth-submit">Enviar link de redefinição</button>
    </form>

    <p class="auth-alt"><a href="{{ route('login') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar para o login</a></p>
</x-layouts.auth>
