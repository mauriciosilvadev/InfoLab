<?php

namespace App\Livewire;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PublicContactForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nome completo')
                    ->required()
                    ->maxLength(120),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required(),
                TextInput::make('subject')
                    ->label('Assunto')
                    ->maxLength(150)
                    ->columnSpanFull(),
                Textarea::make('message')
                    ->label('Mensagem')
                    ->placeholder('Conte-nos como podemos ajudar...')
                    ->rows(6)
                    ->required()
                    ->minLength(10)
                    ->maxLength(2000)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        if (config('mail.default') === null) {
            logger()->info('Mensagem recebida via formulário público', $data);
        } else {
            Mail::raw(
                "Mensagem: {$data['message']}",
                fn ($message) => $message
                    ->to(config('mail.from.address'))
                    ->replyTo($data['email'], $data['name'])
                    ->subject('[Contato Infolab] ' . ($data['subject'] ?: 'Sem assunto'))
            );
        }

        session()->flash('public_contact.status', 'Mensagem enviada com sucesso! Retornaremos em breve.');

        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.public-contact-form');
    }
}
