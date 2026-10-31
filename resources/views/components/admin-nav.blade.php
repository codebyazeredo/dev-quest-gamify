<nav class="admin-nav" aria-label="Configurações">
    <div class="admin-nav-groups">
        @foreach (\App\Support\AdminHubMenu::sections() as $section)
            @continue($section['label'] === 'Visão geral')

            <div class="admin-nav-group">
                <div class="admin-nav-heading">{{ $section['label'] }}</div>

                <ul class="nav flex-column gap-1">
                    @foreach ($section['items'] as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}" @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" />
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</nav>
