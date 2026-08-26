<x-layouts.guest :title="__('Redefinir senha')">
    <h1 class="mb-6 text-lg font-bold tracking-tight text-ink">Redefinir senha</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-terracotta/30 bg-terracotta/10 p-4 text-sm text-terracotta">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium text-ink">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus autocomplete="username"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink">Nova senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-ink">Confirmar nova senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="mt-1 block w-full rounded-lg border border-line bg-card px-3 py-2.5 text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary/30">
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
            Redefinir senha
        </button>
    </form>
</x-layouts.guest>
