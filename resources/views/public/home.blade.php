@extends('layouts.public')

@section('content')
    <section class="bg-white py-20">
        <div class="mx-auto flex max-w-6xl flex-col gap-12 px-6 lg:flex-row lg:items-center">
            <div class="flex-1 space-y-6">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-widest text-slate-500">
                    Centros de Inovação
                </span>
                <h1 class="text-4xl font-bold leading-tight text-slate-900 lg:text-5xl">
                    Conheça os laboratórios e serviços oferecidos pelo Infolab.
                </h1>
                <p class="text-lg text-slate-600">
                    Este espaço centraliza informações sobre os laboratórios do instituto,
                    facilitando o contato, a reserva de recursos e o acompanhamento das atividades.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('public.contact') }}"
                        class="inline-flex items-center rounded-md bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
                    >
                        Entre em contato
                    </a>
                    <a
                        href="#laboratorios"
                        class="inline-flex items-center rounded-md bg-white px-5 py-3 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-100"
                    >
                        Ver laboratórios
                    </a>
                </div>
            </div>
            <div class="flex-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="text-sm font-medium text-slate-500">
                        Última atualização
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-slate-900">
                        {{ now()->translatedFormat('d \\d\\e F \\d\\e Y') }}
                    </div>
                    <p class="mt-6 text-sm leading-relaxed text-slate-600">
                        A equipe do Filament mantém o painel administrativo atualizado para garantir
                        que os dados exibidos aqui reflitam a realidade dos laboratórios.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="laboratorios" class="py-16">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-12 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-semibold text-slate-900">
                        Laboratórios disponíveis
                    </h2>
                    <p class="mt-2 text-slate-600">
                        Detalhes sobre localização, infraestrutura e softwares de cada laboratório.
                    </p>
                </div>
                <span class="text-sm font-medium text-slate-500">
                    {{ trans_choice('{0} Nenhum laboratório cadastrado|{1} :count laboratório|[2,*] :count laboratórios', $laboratories->count(), ['count' => $laboratories->count()]) }}
                </span>
            </div>

            @if ($laboratories->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                    Nenhum laboratório foi cadastrado até o momento. Assim que houver novidades, elas aparecerão aqui.
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($laboratories as $laboratory)
                        @php
                            $galleryPhotos = collect($laboratory->gallery_photo_urls ?? []);
                            $galleryId = \Illuminate\Support\Str::uuid()->toString();
                            $initialPhoto = $laboratory->cover_photo_url ?? $galleryPhotos->first();
                        @endphp
                        <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="relative aspect-4/3 w-full bg-slate-100" data-gallery-root="{{ $galleryId }}">
                                @if ($initialPhoto)
                                    <img
                                        src="{{ $initialPhoto }}"
                                        alt="Foto do laboratório {{ $laboratory->name }}"
                                        class="h-full w-full object-cover"
                                        data-gallery-main
                                        loading="lazy"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-slate-200 text-slate-500" data-gallery-placeholder>
                                        <span class="text-sm font-medium uppercase tracking-widest">
                                            Foto indisponível
                                        </span>
                                    </div>
                                @endif

                                <div class="absolute left-4 top-4 rounded-full bg-white/85 px-3 py-1 text-xs font-semibold text-slate-700 shadow-sm backdrop-blur">
                                    {{ $galleryPhotos->count() }} {{ \Illuminate\Support\Str::plural('foto', $galleryPhotos->count()) }}
                                </div>
                            </div>

                            @if ($galleryPhotos->count() > 0)
                                <div class="flex items-center gap-2 overflow-x-auto px-6 pb-4 pt-4">
                                    @foreach ($galleryPhotos->take(7) as $thumbUrl)
                                        <button
                                            type="button"
                                            class="group relative h-16 w-24 shrink-0 overflow-hidden rounded-lg border transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900"
                                            data-gallery-thumb
                                            data-gallery-id="{{ $galleryId }}"
                                            data-src="{{ $thumbUrl }}"
                                            data-alt="Foto do laboratório {{ $laboratory->name }}"
                                            aria-pressed="{{ $thumbUrl === $initialPhoto ? 'true' : 'false' }}"
                                            @class([
                                                'ring-2 ring-slate-900 border-slate-900' => $thumbUrl === $initialPhoto,
                                                'border-slate-200 hover:border-slate-400' => $thumbUrl !== $initialPhoto,
                                            ])
                                        >
                                            <img
                                                src="{{ $thumbUrl }}"
                                                alt="Miniatura do laboratório {{ $laboratory->name }}"
                                                class="h-full w-full object-cover transition group-hover:scale-105"
                                                loading="lazy"
                                            >
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex flex-1 flex-col gap-4 px-6 pb-6 pt-4">
                            <header class="mb-4">
                                <h3 class="text-xl font-semibold text-slate-900">
                                    {{ $laboratory->name }}
                                </h3>
                                @if ($laboratory->building)
                                    <p class="text-sm text-slate-500">
                                        Localização: {{ $laboratory->building }}
                                    </p>
                                @endif
                            </header>
                            <dl class="flex flex-1 flex-col gap-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                    <dt class="font-medium text-slate-700">Computadores</dt>
                                    <dd>{{ $laboratory->computers_count ?? '—' }}</dd>
                                </div>

                                @php
                                    $softwareList = collect($laboratory->softwares ?? [])
                                        ->filter()
                                        ->map(fn ($software) => trim($software));
                                @endphp
                                <div>
                                    <dt class="font-medium text-slate-700">
                                        Softwares disponíveis
                                    </dt>
                                    <dd class="mt-1 text-sm text-slate-600">
                                        @if ($softwareList->isEmpty())
                                            Não informado.
                                        @else
                                            <ul class="list-disc space-y-1 pl-5">
                                                @foreach ($softwareList as $software)
                                                    <li>{{ $software }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-gallery-root]').forEach(root => {
                    const galleryId = root.dataset.galleryRoot;
                    const mainImage = root.querySelector('[data-gallery-main]');
                    const placeholder = root.querySelector('[data-gallery-placeholder]');

                    document.querySelectorAll(`[data-gallery-thumb][data-gallery-id="${galleryId}"]`).forEach(button => {
                        button.addEventListener('click', () => {
                            const src = button.dataset.src;
                            const alt = button.dataset.alt ?? '';

                            if (mainImage) {
                                mainImage.src = src;
                                mainImage.alt = alt;
                                mainImage.classList.remove('hidden');
                            }

                            if (placeholder) {
                                placeholder.classList.add('hidden');
                            }

                            document.querySelectorAll(`[data-gallery-thumb][data-gallery-id="${galleryId}"]`).forEach(btn => {
                                btn.classList.remove('ring-2', 'ring-slate-900', 'border-slate-900');
                                btn.classList.remove('border-slate-200', 'hover:border-slate-400');
                                btn.classList.add('border-slate-200', 'hover:border-slate-400');
                                btn.setAttribute('aria-pressed', 'false');
                            });

                            button.classList.remove('border-slate-200', 'hover:border-slate-400');
                            button.classList.add('ring-2', 'ring-slate-900', 'border-slate-900');
                            button.setAttribute('aria-pressed', 'true');
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce

