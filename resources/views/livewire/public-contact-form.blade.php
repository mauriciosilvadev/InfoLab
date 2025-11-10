<div class="space-y-6">
    @if (session()->has('public_contact.status'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('public_contact.status') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="grid gap-6">
            {{ $this->form }}
        </div>

        <div>
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
            >
                Enviar mensagem
            </button>
        </div>
    </form>
</div>

