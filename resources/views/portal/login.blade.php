<x-layouts.guest title="Portal do cliente">
    <h1 class="h5 text-center mb-1">Portal do cliente</h1>
    <p class="text-body-secondary text-center mb-4">Acompanhe e abra chamados de suporte com o CNPJ da sua empresa.</p>

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
            <input id="cnpj" type="text" name="cnpj" x-model="cnpj" x-on:input="cnpj = formatCnpj(cnpj)" class="form-control" inputmode="numeric" placeholder="00.000.000/0000-00" required autofocus>
        </div>

        <div>
            <label for="email" class="form-label fw-medium small mb-1">E-mail cadastrado</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="contato@suaempresa.com" required autocomplete="email">
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
</x-layouts.guest>
