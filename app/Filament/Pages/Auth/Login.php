<?php

namespace App\Filament\Pages\Auth;

use App\Exceptions\Auth\DirectoryAuthenticationException;
use App\Services\Auth\LdapAuthenticator;
use App\Services\Auth\SystemAccountAuthenticator;
use App\Services\Auth\UserSynchronizer;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\MultiFactor\Contracts\HasBeforeChallengeHook;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Filament\Schemas\Schema;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getUsernameFormComponent(): TextInput
    {
        return TextInput::make('username')
            ->label('Nome de usuário')
            ->required()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $credentials = [
            'username' => $data['username'],
            'password' => $data['password'],
        ];

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $systemAccountAuthenticator = app(SystemAccountAuthenticator::class);

        $user = null;
        $systemUser = $systemAccountAuthenticator->find($credentials['username']);

        if ($systemUser) {
            if (! $systemAccountAuthenticator->verifyPassword($systemUser, $credentials['password'])) {
                $this->userUndertakingMultiFactorAuthentication = null;
                $this->fireFailedEvent($authGuard, $systemUser, $credentials);

                throw ValidationException::withMessages([
                    'data.password' => 'Senha inválida',
                ]);
            }

            $user = $systemUser;
        } else {
            $LdapAuthenticator = app(LdapAuthenticator::class);

            try {
                $LdapUser = $LdapAuthenticator->authenticate($credentials['username'], $credentials['password']);
            } catch (DirectoryAuthenticationException $exception) {
                $this->userUndertakingMultiFactorAuthentication = null;
                $this->fireFailedEvent($authGuard, null, $credentials);

                $field = match ($exception->reason) {
                    'invalid_credentials' => 'data.password',
                    'user_not_found' => 'data.username',
                    default => 'data.username',
                };

                throw ValidationException::withMessages([
                    $field => $exception->getMessage(),
                ]);
            }

            $user = app(UserSynchronizer::class)->sync($LdapUser);
        }

        if (
            $user instanceof FilamentUser &&
            ! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())
        ) {
            $this->fireFailedEvent($authGuard, $user, $credentials);
            $this->throwFailureValidationException();
        }

        if (
            filled($this->userUndertakingMultiFactorAuthentication) &&
            (decrypt($this->userUndertakingMultiFactorAuthentication) === $user->getAuthIdentifier())
        ) {
            $this->multiFactorChallengeForm->validate();
        } else {
            foreach (Filament::getMultiFactorAuthenticationProviders() as $multiFactorAuthenticationProvider) {
                if (! $multiFactorAuthenticationProvider->isEnabled($user)) {
                    continue;
                }

                $this->userUndertakingMultiFactorAuthentication = encrypt($user->getAuthIdentifier());

                if ($multiFactorAuthenticationProvider instanceof HasBeforeChallengeHook) {
                    $multiFactorAuthenticationProvider->beforeChallenge($user);
                }

                break;
            }

            if (filled($this->userUndertakingMultiFactorAuthentication)) {
                $this->multiFactorChallengeForm->fill();

                return null;
            }
        }

        $authGuard->login($user, $data['remember'] ?? false);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    protected function fireFailedEvent(Guard $guard, ?Authenticatable $user, #[SensitiveParameter] array $credentials): void
    {
        event(app(Failed::class, ['guard' => property_exists($guard, 'name') ? $guard->name : '', 'user' => $user, 'credentials' => $credentials]));
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
