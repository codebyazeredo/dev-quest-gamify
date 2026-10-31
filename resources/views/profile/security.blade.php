<x-layouts.app>
    <div>
        <x-page-header title="Meu perfil" subtitle="Proteja sua conta e escolha como quer ser avisado." />

        <x-account-tabs />

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($recoveryCodes)
            <div class="alert alert-warning" role="alert">
                <strong>Guarde estes códigos de recuperação.</strong> Cada um vale uma única vez e só são exibidos agora.
                <ul class="list-unstyled font-monospace mt-2 mb-0">
                    @foreach ($recoveryCodes as $recoveryCode)
                        <li>{{ $recoveryCode }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-card class="mb-4">
            <h2 class="h6 fw-semibold mb-3">Notificações por e-mail</h2>

            <form method="POST" action="{{ route('preferences.email') }}" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="email_notifications" value="{{ auth()->user()->email_notifications ? 0 : 1 }}">

                <p class="mb-0">
                    <x-badge :color="auth()->user()->email_notifications ? 'success' : 'secondary'">{{ auth()->user()->email_notifications ? 'Ativadas' : 'Desativadas' }}</x-badge>
                    E-mail quando alguém atribuir uma tarefa a você.
                </p>

                <x-button type="submit" variant="secondary">{{ auth()->user()->email_notifications ? 'Desativar e-mails' : 'Ativar e-mails' }}</x-button>
            </form>
        </x-card>

        <x-card>
            <h2 class="h6 fw-semibold mb-3">Autenticação em dois fatores</h2>

            @if (auth()->user()->hasTwoFactorEnabled())
                <p><x-badge color="success">Ativada</x-badge> Ao entrar, será pedido um código do aplicativo autenticador.</p>
                <p class="small text-body-secondary">Códigos de recuperação restantes: {{ count(auth()->user()->two_factor_recovery_codes ?? []) }}</p>

                <form method="POST" action="{{ route('security.two-factor.disable') }}" class="row g-2 align-items-end">
                    @csrf
                    @method('DELETE')

                    <div class="col-sm-6 col-md-4">
                        <label for="disable-password" class="form-label fw-medium small mb-1">Confirme sua senha para desativar</label>
                        <input type="password" id="disable-password" name="password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="col-auto">
                        <x-button type="submit" variant="danger">Desativar</x-button>
                    </div>
                </form>
            @elseif ($pendingSecret)
                <ol class="mb-3">
                    <li>No aplicativo autenticador (Google Authenticator, Authy, 1Password...), adicione uma conta manualmente com esta chave:
                        <div class="my-2"><code class="fs-5 user-select-all">{{ implode(' ', str_split($pendingSecret, 4)) }}</code></div>
                        <div class="small text-body-secondary">Tipo: baseado em tempo. Ou use o endereço: <code class="user-select-all text-break">{{ $provisioningUri }}</code></div>
                    </li>
                    <li class="mt-2">Digite abaixo o código de 6 dígitos que o aplicativo exibir.</li>
                </ol>

                <form method="POST" action="{{ route('security.two-factor.confirm') }}" class="row g-2 align-items-end">
                    @csrf

                    <div class="col-sm-6 col-md-3">
                        <label for="confirm-code" class="form-label fw-medium small mb-1">Código</label>
                        <input type="text" id="confirm-code" name="code" class="form-control" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required>
                    </div>
                    <div class="col-auto">
                        <x-button type="submit">Confirmar e ativar</x-button>
                    </div>
                </form>
            @else
                <p class="text-body-secondary">A autenticação em dois fatores está desativada. Recomendado principalmente para administradores.</p>

                <form method="POST" action="{{ route('security.two-factor.enable') }}">
                    @csrf
                    <x-button type="submit"><i class="bi bi-shield-lock" aria-hidden="true"></i> Configurar</x-button>
                </form>
            @endif
        </x-card>
    </div>
</x-layouts.app>
