<x-layouts.guest :title="__('Redefinir senha')">
    <h1 class="h4 mb-4">Redefinir senha</h1>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="vstack gap-3">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="form-label fw-medium small mb-1">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" class="form-control" required autofocus autocomplete="username">
        </div>

        <div>
            <label for="password" class="form-label fw-medium small mb-1">Nova senha</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <div>
            <label for="password_confirmation" class="form-label fw-medium small mb-1">Confirmar nova senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100">Redefinir senha</button>
    </form>
</x-layouts.guest>
