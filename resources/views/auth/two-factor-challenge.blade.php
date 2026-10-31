<x-layouts.auth :title="__('Verificação em dois fatores')">
    <h1 class="auth-title">Verificação em dois fatores</h1>
    <p class="auth-lead">Digite o código de 6 dígitos do seu aplicativo autenticador ou um código de recuperação.</p>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.challenge') }}" class="vstack gap-3">
        @csrf

        <div>
            <label for="code" class="form-label fw-medium small mb-1">Código</label>
            <input id="code" type="text" name="code" class="form-control text-center fs-5" inputmode="numeric" autocomplete="one-time-code" maxlength="20" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100 auth-submit">Verificar <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
    </form>

    <p class="auth-alt"><a href="{{ route('login') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar ao login</a></p>
</x-layouts.auth>
