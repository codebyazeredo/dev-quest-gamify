<x-layouts.guest :title="__('Entrar')">
    <h1 class="mb-6 text-2xl font-bold tracking-tight text-ink">Faça seu login</h1>

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
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink-muted">
                    <x-icon name="mail" class="h-4 w-4" />
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="seu@email.com"
                    class="block w-full rounded-lg border border-line bg-card py-2.5 pl-10 pr-3 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
            </div>
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-ink">Senha</label>
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink-muted">
                    <x-icon name="lock" class="h-4 w-4" />
                </span>
                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password" placeholder="sua senha"
                    class="block w-full rounded-lg border border-line bg-card py-2.5 pl-10 pr-10 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
                <button type="button" @click="show = !show" title="Mostrar/ocultar senha" aria-label="Mostrar/ocultar senha" class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-muted hover:text-ink">
                    <span x-show="!show"><x-icon name="eye" class="h-4 w-4" /></span>
                    <span x-show="show" x-cloak><x-icon name="eye-off" class="h-4 w-4" /></span>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-ink-muted">
                <input type="checkbox" name="remember" class="rounded border-line">
                Lembrar de mim
            </label>

            <a href="{{ route('password.request') }}" class="text-primary hover:underline">Esqueceu sua senha?</a>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary px-4 py-3 text-sm font-semibold text-white hover:bg-primary-hover">
            Entrar
        </button>
    </form>
</x-layouts.guest>
