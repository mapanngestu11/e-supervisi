@php
    $menuItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-chart-line'],
        ['route' => 'institusi', 'label' => 'Institusi', 'icon' => 'fa-building-columns'],
        ['route' => 'guru', 'label' => 'Database Guru', 'icon' => 'fa-user-tie'],
        ['route' => 'instrumen', 'label' => 'Instrumen', 'icon' => 'fa-clipboard-list'],
        ['route' => 'observasi', 'label' => 'Observasi', 'icon' => 'fa-eye'],
        ['route' => 'laporan', 'label' => 'Laporan', 'icon' => 'fa-print'],
    ];
    $active = $activeMenu ?? '';
@endphp

<aside class="w-full shrink-0 lg:w-64">
    <nav class="rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm">
        <p class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Main Menu</p>
        <ul class="space-y-1">
            @foreach ($menuItems as $item)
                @php
                    $isActive = $active === $item['route'];
                @endphp
                <li>
                    <a
                        href="{{ route($item['route']) }}"
                        @class([
                            'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-[#1e3a5f] text-white shadow-sm' => $isActive,
                            'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! $isActive,
                        ])
                    >
                        <i @class(['fa-solid w-5 text-center', $item['icon'], 'text-white' => $isActive, 'text-slate-500' => ! $isActive])></i>
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</aside>
