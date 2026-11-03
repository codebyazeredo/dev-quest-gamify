<x-layouts.auth title="Portal de Clientes" :portal="true">
    <header class="auth-header">
        <span class="auth-badge" aria-hidden="true"><i class="bi bi-building"></i></span>
        <h1 class="auth-title">Portal de Clientes</h1>
        <p class="auth-lead">Acompanhe e abra chamados de suporte com o CNPJ da sua empresa.</p>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.login.store') }}" class="vstack gap-3" x-data="cnpjInput(@js(old('cnpj', '')))">
        @csrf

        <div>
            <label for="cnpj" class="form-label fw-medium small mb-1">CNPJ</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-card-text" aria-hidden="true"></i></span>
                <input id="cnpj" type="text" name="cnpj" x-model="cnpj" x-on:input="cnpj = formatCnpj(cnpj)" class="form-control" inputmode="numeric" placeholder="00.000.000/0000-00" required autofocus>
            </div>
        </div>

        <div>
            <label for="email" class="form-label fw-medium small mb-1">E-mail cadastrado</label>
            <div class="input-group">
                <span class="input-group-text"><x-icon name="mail" /></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="contato@suaempresa.com" required autocomplete="email">
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 auth-submit">Entrar <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
    </form>

    <div class="auth-divider"><span>Faz parte da equipe?</span></div>

    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 auth-secondary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar ao login da equipe
    </a>
</x-layouts.auth>
