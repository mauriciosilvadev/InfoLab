<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ trim(($title ?? null) ? "{$title} | " : '') . config('app.name', 'Infolab') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @livewireStyles
        @filamentStyles
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col">
        @php
            $isFilamentAuthenticated = auth()->check();
            $panelPath = 'admin';
        @endphp

        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between">
                <a href="{{ url('/') }}" class="text-lg font-semibold tracking-tight text-slate-900">
                    {{ config('app.name', 'Infolab') }}
                </a>

                <nav class="flex flex-1 flex-wrap items-center gap-4 text-sm font-medium text-slate-600 md:justify-end">
                    <a
                        href="{{ url('/') }}"
                        @class([
                            'transition-colors hover:text-slate-900',
                            'text-slate-900' => request()->routeIs('public.home'),
                        ])
                    >
                        Início
                    </a>

                    <a
                        href="{{ route('public.contact') }}"
                        @class([
                            'transition-colors hover:text-slate-900',
                            'text-slate-900' => request()->routeIs('public.contact'),
                        ])
                    >
                        Contato
                    </a>

                    <span
                        @class([
                            'inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-widest transition',
                            $isFilamentAuthenticated
                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                : 'border-slate-200 bg-slate-100 text-slate-600',
                        ])
                    >
                        Painel:
                        <span>
                            {{ $isFilamentAuthenticated ? 'logado' : 'não logado' }}
                        </span>
                    </span>

                    @if ($isFilamentAuthenticated)
                        <a
                            href="{{ url($panelPath) }}"
                            class="inline-flex items-center rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-slate-700"
                        >
                            Acessar painel
                        </a>
                    @else
                        <a
                            href="{{ url($panelPath . '/login') }}"
                            class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-600 transition hover:border-slate-300 hover:text-slate-900"
                        >
                            Entrar
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-6xl px-6 py-6 text-sm text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'Infolab') }}. Todos os direitos reservados.
            </div>
        </footer>

        @livewireScripts
        @filamentScripts
        @stack('scripts')
    </body>
</html>

