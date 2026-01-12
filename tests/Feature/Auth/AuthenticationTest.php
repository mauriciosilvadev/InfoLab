<?php

namespace Tests\Feature\Auth;

use App\Exceptions\Auth\DirectoryAuthenticationException;
use App\Filament\Pages\Auth\Login;
use App\Models\User;
use App\Models\UserPreregistration;
use App\Services\Auth\LdapAuthenticator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_admin_can_authenticate_locally(): void
    {
        $password = 'password';
        $admin = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt($password),
        ]);
        $admin->assignRole(User::ADMIN_ROLE);

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('authenticate');
        });

        Livewire::test(Login::class)
            ->set('data.username', 'admin')
            ->set('data.password', $password)
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($admin);
    }

    public function test_system_vigia_can_authenticate_locally(): void
    {
        $password = 'password';
        $vigia = User::factory()->create([
            'username' => 'vigia',
            'password' => bcrypt($password),
        ]);
        $vigia->assignRole(User::VIGIA_ROLE);

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('authenticate');
        });

        Livewire::test(Login::class)
            ->set('data.username', 'vigia')
            ->set('data.password', $password)
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($vigia);
    }

    public function test_system_sugrad_can_authenticate_locally(): void
    {
        $password = 'password';
        $sugrad = User::factory()->create([
            'username' => 'sugrad',
            'password' => bcrypt($password),
        ]);
        $sugrad->assignRole(User::SUGRAD_ROLE);

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('authenticate');
        });

        Livewire::test(Login::class)
            ->set('data.username', 'sugrad')
            ->set('data.password', $password)
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($sugrad);
    }

    public function test_system_user_fails_with_wrong_password_and_does_not_hit_ldap(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('correct-password'),
        ]);
        $admin->assignRole(User::ADMIN_ROLE);

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('authenticate');
        });

        Livewire::test(Login::class)
            ->set('data.username', 'admin')
            ->set('data.password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors(['data.password']);

        $this->assertGuest();
    }

    public function test_ldap_user_is_created_and_logged_in_if_credentials_are_valid(): void
    {
        $username = 'mauricio.s.souza';
        $password = 'secret';

        $ldapReturnData = [
            'username' => $username,
            'name' => 'Maurício Souza',
            'email' => 'mauricio.s.souza@edu.ufes.br',
            'is_teacher' => false,
            'raw' => [],
        ];

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) use ($username, $password, $ldapReturnData) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->with($username, $password)
                ->andReturn($ldapReturnData);
        });

        Livewire::test(Login::class)
            ->set('data.username', $username)
            ->set('data.password', $password)
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email' => 'mauricio.s.souza@edu.ufes.br',
            'name' => 'Maurício Souza',
        ]);

        $this->assertAuthenticated();

        $user = User::where('username', $username)->first();
        $this->assertTrue($user->hasRole(User::USER_ROLE));
    }

    public function test_ldap_teacher_is_assigned_teacher_role_automatically(): void
    {
        $username = 'prof.girafales';

        $ldapReturnData = [
            'username' => $username,
            'name' => 'Prof Girafales',
            'email' => 'girafales@ufes.br',
            'is_teacher' => true,
            'raw' => [],
        ];

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) use ($ldapReturnData) {
            $mock->shouldReceive('authenticate')->andReturn($ldapReturnData);
        });

        Livewire::test(Login::class)
            ->set('data.username', $username)
            ->set('data.password', '123')
            ->call('authenticate');

        $user = User::where('username', $username)->first();
        $this->assertTrue($user->hasRole(User::TEACHER_ROLE));
    }

    public function test_existing_user_is_synced_with_new_ldap_data(): void
    {
        $user = User::factory()->create([
            'username' => 'joao.silva',
            'name' => 'Joao Antigo',
            'email' => 'joao@antigo.com',
        ]);

        $ldapReturnData = [
            'username' => 'joao.silva',
            'name' => 'Joao Atualizado',
            'email' => 'joao@novo.com',
            'is_teacher' => false,
        ];

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) use ($ldapReturnData) {
            $mock->shouldReceive('authenticate')->andReturn($ldapReturnData);
        });

        Livewire::test(Login::class)
            ->set('data.username', 'joao.silva')
            ->set('data.password', 'password')
            ->call('authenticate');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Joao Atualizado',
            'email' => 'joao@novo.com',
        ]);
    }

    public function test_preregistered_user_receives_correct_role(): void
    {
        $email = 'futuro.admin@ufes.br';

        UserPreregistration::create([
            'email' => $email,
            'role' => User::ADMIN_ROLE,
        ]);

        $ldapReturnData = [
            'username' => 'futuro.admin',
            'name' => 'Futuro Admin',
            'email' => $email,
            'is_teacher' => false,
        ];

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) use ($ldapReturnData) {
            $mock->shouldReceive('authenticate')->andReturn($ldapReturnData);
        });

        Livewire::test(Login::class)
            ->set('data.username', 'futuro.admin')
            ->set('data.password', 'password')
            ->call('authenticate');

        $user = User::where('email', $email)->first();
        $this->assertTrue($user->hasRole(User::ADMIN_ROLE));

        $this->assertDatabaseMissing('user_preregistrations', ['email' => $email]);
    }

    public function test_preregistered_teacher_user_receives_correct_role(): void
    {
        $email = 'prof.girafales@ufes.br';

        UserPreregistration::create([
            'email' => $email,
            'role' => User::ADMIN_ROLE,
        ]);

        $ldapReturnData = [
            'username' => 'prof.girafales',
            'name' => 'Prof Girafales',
            'email' => $email,
            'is_teacher' => true,
        ];

        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) use ($ldapReturnData) {
            $mock->shouldReceive('authenticate')->andReturn($ldapReturnData);
        });

        Livewire::test(Login::class)
            ->set('data.username', 'prof.girafales')
            ->set('data.password', 'password')
            ->call('authenticate');

        $user = User::where('email', $email)->first();
        $this->assertTrue($user->hasRole(User::ADMIN_ROLE));

        $this->assertDatabaseMissing('user_preregistrations', ['email' => $email]);
    }

    public function test_ldap_authentication_failure_shows_error(): void
    {
        $this->mock(LdapAuthenticator::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->andThrow(DirectoryAuthenticationException::invalidCredentials());
        });

        Livewire::test(Login::class)
            ->set('data.username', 'usuario.inexistente')
            ->set('data.password', 'senha-errada')
            ->call('authenticate')
            ->assertHasErrors(['data.password']);

        $this->assertGuest();
    }
}
