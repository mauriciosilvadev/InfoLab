<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Models\UserPreregistration;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Pré-registrar Usuário';
    }

    public function getBreadcrumb(): string
    {
        return 'Pré-registrar Usuário';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique('user_preregistrations', 'email')
                    ->helperText('E-mail do usuário que será pré-registrado. O perfil será atribuído na primeira autenticação via LDAP.')
                    ->maxLength(255),
                Select::make('role')
                    ->label('Perfil')
                    ->required()
                    ->default(User::ADMIN_ROLE)
                    ->options([
                        User::ADMIN_ROLE => 'Administrador',
                        User::TEACHER_ROLE => 'Professor',
                        User::USER_ROLE => 'Usuário Comum',
                    ])
                    ->helperText('Perfil que será atribuído ao usuário na primeira autenticação.')
                    ->native(false),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        UserPreregistration::create($data);

        return new User;
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->title('Pré-registro criado com sucesso')
            ->success()
            ->send();
    }

    protected function afterCreate(): void
    {
        //
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
