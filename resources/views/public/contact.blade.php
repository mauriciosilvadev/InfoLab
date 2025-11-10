@extends('layouts.public')

@section('content')
    <section class="bg-gradient-to-b from-slate-900 to-slate-800 py-20 text-white">
        <div class="mx-auto max-w-4xl px-6 text-center">
            <h1 class="text-4xl font-semibold leading-tight lg:text-5xl">
                Fale com a equipe do Infolab
            </h1>
            <p class="mt-4 text-lg text-slate-200">
                Envie suas dúvidas, sugestões ou pedidos de suporte. Nossa equipe retornará o contato o mais breve possível.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto grid max-w-6xl gap-12 px-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <livewire:public-contact-form />
            </div>
            <aside class="space-y-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">
                        Outros canais de atendimento
                    </h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Caso prefira, entre em contato pelos meios tradicionais de atendimento.
                    </p>
                </div>

                <dl class="space-y-6 text-sm text-slate-600">
                    <div>
                        <dt class="font-semibold text-slate-800">E-mail institucional</dt>
                        <dd class="mt-1">
                            <a href="mailto:suporte@infolab.example" class="text-slate-900 hover:underline">
                                suporte@infolab.example
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-800">Telefone</dt>
                        <dd class="mt-1">
                            (99) 1234-5678
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-800">Endereço</dt>
                        <dd class="mt-1 leading-relaxed">
                            Bloco de Tecnologia, Sala 101<br>
                            Avenida do Conhecimento, 500<br>
                            Campus Universitário – Cidade/UF
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-800">Horário de atendimento</dt>
                        <dd class="mt-1">
                            Segunda a sexta, das 8h às 18h.
                        </dd>
                    </div>
                </dl>
            </aside>
        </div>
    </section>
@endsection

