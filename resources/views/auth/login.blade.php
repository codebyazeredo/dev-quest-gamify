<x-layouts.guest :title="__('Entrar')">
    <h1 class="mb-6 text-xl font-bold tracking-tight text-ink">Entrar</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-terracotta/30 bg-terracotta/10 p-4 text-sm text-terracotta">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-ink">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-ink-muted">
                <input type="checkbox" name="remember" class="rounded border-line">
                Lembrar de mim
            </label>

            <a href="{{ route('password.request') }}" class="text-primary hover:underline">Esqueceu sua senha?</a>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover">
            Entrar
        </button>
    </form>
</x-layouts.guest>
