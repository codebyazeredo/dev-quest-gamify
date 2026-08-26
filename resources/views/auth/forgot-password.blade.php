<x-layouts.guest :title="__('Esqueci minha senha')">
    <h1 class="mb-2 text-lg font-bold tracking-tight text-ink">Esqueceu sua senha?</h1>
    <p class="mb-6 text-sm text-ink-muted">
        Informe seu e-mail e enviaremos um link para redefinir sua senha.
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-terracotta/30 bg-terracotta/10 p-4 text-sm text-terracotta">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-ink">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
            Enviar link de redefinição de senha
        </button>
    </form>

    <p class="mt-4 text-sm text-ink-muted">
        <a href="{{ route('login') }}" class="text-primary hover:underline">Voltar para o login</a>
    </p>
</x-layouts.guest>
