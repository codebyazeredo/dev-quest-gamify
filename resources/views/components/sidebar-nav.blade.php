<nav class="flex flex-col gap-1">
    <a href="{{ route('boards.index') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('boards.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
        Quadros
    </a>

    @if (auth()->user()->isAdmin())
        @php
            $navLink = fn (string $route, string $label) => [
                'route' => $route,
                'label' => $label,
                'active' => request()->routeIs($route),
            ];
            $gestaoLinks = [
                $navLink('admin.users', 'Usuários'),
                $navLink('admin.people', 'Pessoas'),
                $navLink('admin.roles', 'Roles'),
            ];
            $gamificacaoLinks = [
                $navLink('admin.categories', 'Categorias'),
                $navLink('admin.event-rules', 'Regras de XP'),
                $navLink('admin.priority-rules', 'Gravidade'),
                $navLink('admin.achievements', 'Conquistas'),
                $navLink('admin.titles', 'Títulos'),
                $navLink('admin.challenges', 'Desafios'),
            ];
            $sistemaLinks = [
                $navLink('admin.settings', 'Configurações'),
            ];
        @endphp

        <x-sidebar-group label="Gestão" :active="collect($gestaoLinks)->contains('active', true)">
            @foreach ($gestaoLinks as $link)
                <a href="{{ route($link['route']) }}" class="rounded-md px-3 py-2 text-sm font-medium {{ $link['active'] ? 'bg-primary text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </x-sidebar-group>

        <x-sidebar-group label="Gamificação" :active="collect($gamificacaoLinks)->contains('active', true)">
            @foreach ($gamificacaoLinks as $link)
                <a href="{{ route($link['route']) }}" class="rounded-md px-3 py-2 text-sm font-medium {{ $link['active'] ? 'bg-primary text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </x-sidebar-group>

        <x-sidebar-group label="Sistema" :active="collect($sistemaLinks)->contains('active', true)">
            @foreach ($sistemaLinks as $link)
                <a href="{{ route($link['route']) }}" class="rounded-md px-3 py-2 text-sm font-medium {{ $link['active'] ? 'bg-primary text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </x-sidebar-group>
    @endif
</nav>
