<?php

namespace Tests\Unit\Services\Auth;

namespace App\Services\Auth;

$mockLdapConnectReturn = true;
$mockLdapBindReturn = true;
$mockLdapSearchReturn = true;
$mockLdapGetEntriesReturn = [];

function ldap_connect($host, $port)
{
    global $mockLdapConnectReturn;

    return $mockLdapConnectReturn ? fopen('php://memory', 'r') : false;
}

function ldap_set_option($link, $option, $newval)
{
    return true;
}

function ldap_bind($link, $bind_rdn = null, $bind_password = null)
{
    global $mockLdapBindReturn;

    return $mockLdapBindReturn;
}

function ldap_search($link, $base_dn, $filter, $attributes = [])
{
    global $mockLdapSearchReturn;

    return $mockLdapSearchReturn;
}

function ldap_get_entries($link, $result)
{
    global $mockLdapGetEntriesReturn;

    return $mockLdapGetEntriesReturn;
}

function ldap_unbind($link)
{
    return true;
}

namespace Tests\Unit\Services\Auth;

use App\Exceptions\Auth\DirectoryAuthenticationException;
use App\Services\Auth\LdapAuthenticator;
use Tests\TestCase as LaravelTestCase;

class LdapAuthenticatorTest extends LaravelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        global $mockLdapConnectReturn, $mockLdapBindReturn, $mockLdapSearchReturn, $mockLdapGetEntriesReturn;
        $mockLdapConnectReturn = true;
        $mockLdapBindReturn = true;
        $mockLdapSearchReturn = true;
        $mockLdapGetEntriesReturn = ['count' => 0];
    }

    public function test_it_throws_exception_if_connection_fails()
    {
        global $mockLdapConnectReturn;
        $mockLdapConnectReturn = false;

        $this->expectException(DirectoryAuthenticationException::class);
        $this->expectExceptionMessage('Não foi possível conectar ao servidor LDAP.');

        $authenticator = new LdapAuthenticator(['host' => '127.0.0.1']);
        $authenticator->authenticate('user', 'pass');
    }

    public function test_it_throws_exception_if_credentials_are_invalid()
    {
        global $mockLdapBindReturn;
        $mockLdapBindReturn = false;

        $this->expectException(DirectoryAuthenticationException::class);

        $authenticator = new LdapAuthenticator;
        $authenticator->authenticate('user', 'wrong_pass');
    }

    public function test_it_maps_ldap_entry_correctly_single_affiliation()
    {
        global $mockLdapGetEntriesReturn;
        $mockLdapGetEntriesReturn = [
            'count' => 1,
            0 => [
                'displayname' => ['count' => 1, 0 => 'Mauricio Souza'],
                'mail' => ['count' => 1, 0 => 'mauricio@ufes.br'],
                'brpersoncpf' => ['count' => 1, 0 => '12345678900'],
                'edupersonaffiliation' => ['count' => 1, 0 => '4'],
            ],
        ];

        $authenticator = new LdapAuthenticator;
        $user = $authenticator->authenticate('mauricio', 'pass');

        $this->assertEquals('Mauricio Souza', $user['name']);
        $this->assertEquals('mauricio@ufes.br', $user['email']);
        $this->assertEquals('12345678900', $user['cpf']);
        $this->assertFalse($user['is_teacher']);
    }

    public function test_it_identifies_teacher_correctly_single_affiliation()
    {
        global $mockLdapGetEntriesReturn;
        $mockLdapGetEntriesReturn = [
            'count' => 1,
            0 => [
                'displayname' => ['count' => 1, 0 => 'Prof Girafales'],
                'edupersonaffiliation' => ['count' => 1, 0 => '1'],
            ],
        ];

        $authenticator = new LdapAuthenticator;
        $user = $authenticator->authenticate('girafales', 'pass');

        $this->assertTrue($user['is_teacher']);
    }

    public function test_it_identifies_teacher_correctly_multiple_affiliations()
    {
        global $mockLdapGetEntriesReturn;
        $mockLdapGetEntriesReturn = [
            'count' => 1,
            0 => [
                'displayname' => ['count' => 1, 0 => 'Prof Ex Aluno'],
                'edupersonaffiliation' => [
                    'count' => 2,
                    0 => '19',
                    1 => '1',
                ],
            ],
        ];

        $authenticator = new LdapAuthenticator;
        $user = $authenticator->authenticate('prof.exaluno', 'pass');

        $this->assertTrue($user['is_teacher'], 'Deveria ser identificado como professor mesmo tendo outros vínculos.');
    }

    public function test_it_handles_multiple_affiliations_without_teacher_role()
    {
        global $mockLdapGetEntriesReturn;
        $mockLdapGetEntriesReturn = [
            'count' => 1,
            0 => [
                'displayname' => ['count' => 1, 0 => 'Tecnico Aluno'],
                'edupersonaffiliation' => [
                    'count' => 2,
                    0 => '2',
                    1 => '4',
                ],
            ],
        ];

        $authenticator = new LdapAuthenticator;
        $user = $authenticator->authenticate('tecnico', 'pass');

        $this->assertFalse($user['is_teacher']);
    }

    public function test_it_handles_missing_optional_fields_gracefully()
    {
        global $mockLdapGetEntriesReturn;
        $mockLdapGetEntriesReturn = [
            'count' => 1,
            0 => [
                'cn' => ['count' => 1, 0 => 'Usuario Basico'],
            ],
        ];

        $authenticator = new LdapAuthenticator;
        $user = $authenticator->authenticate('basico', 'pass');

        $this->assertEquals('Usuario Basico', $user['name']);
        $this->assertNull($user['email']);
        $this->assertNull($user['cpf']);
        $this->assertFalse($user['is_teacher']);
    }
}
