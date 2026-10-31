<ul class="nav nav-underline mb-4" aria-label="Minha conta">
    <li class="nav-item">
        <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" @if (request()->routeIs('profile')) aria-current="page" @endif>Dados pessoais</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('security') }}" class="nav-link {{ request()->routeIs('security') ? 'active' : '' }}" @if (request()->routeIs('security')) aria-current="page" @endif>Segurança e notificações</a>
    </li>
</ul>
