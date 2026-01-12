<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\DirectoryAuthenticationException;

use function array_change_key_case;
use function is_resource;

class LdapAuthenticator
{
    /**
     * @var array{host:string,port:int,base_dn:string,bind_dn:?string,bind_password:?string}
     */
    private array $config;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = [
            'host' => $config['host'] ?? config('ufes_ldap.host'),
            'port' => $config['port'] ?? config('ufes_ldap.port'),
            'base_dn' => $config['base_dn'] ?? config('ufes_ldap.base_dn'),
            'bind_dn' => $config['bind_dn'] ?? config('ufes_ldap.bind_dn'),
            'bind_password' => $config['bind_password'] ?? config('ufes_ldap.bind_password'),
        ];
    }

    /**
     * @return array{
     * username:string,
     * name:?string,
     * email:?string,
     * alternative_email:?string,
     * cpf:?string,
     * matricula:?string,
     * status:?string,
     * is_teacher:bool,
     * raw:array<string,mixed>
     * }
     */
    public function authenticate(string $username, #[\SensitiveParameter] string $password): array
    {
        $connection = $this->connect();

        try {
            $this->bindWithConfiguredCredentials($connection);

            $userDn = $this->buildUserDn($username);

            if (! @ldap_bind($connection, $userDn, $password)) {
                throw DirectoryAuthenticationException::invalidCredentials();
            }

            $search = @ldap_search(
                $connection,
                $userDn,
                $this->buildUserFilter($username),
                [
                    'displayName',
                    'cn',
                    'givenName',
                    'sn',
                    'mail',
                    'mailForwardingAddress',
                    'brPersonCPF',
                    'matGrad',
                    'eduPersonScopedAffiliation',
                    'eduPersonAffiliation',
                ]
            );

            if ($search === false) {
                throw DirectoryAuthenticationException::searchFailed();
            }

            $entries = ldap_get_entries($connection, $search);
            $count = $entries['count'] ?? 0;

            if ($entries === false || $count === 0) {
                throw DirectoryAuthenticationException::userNotFound();
            }

            $entry = array_change_key_case($entries[0] ?? [], CASE_LOWER);

            return $this->mapEntryToUserData($username, $entry);
        } catch (DirectoryAuthenticationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw DirectoryAuthenticationException::connectionFailed($exception->getMessage(), $exception);
        } finally {
            $this->disconnect($connection);
        }
    }

    /**
     * @return \LDAP\Connection|resource
     */
    private function connect()
    {
        $connection = @ldap_connect($this->config['host'], $this->config['port']);

        if ($connection === false) {
            throw DirectoryAuthenticationException::connectionFailed();
        }

        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

        return $connection;
    }

    /**
     * @param  \LDAP\Connection|resource  $connection
     */
    private function bindWithConfiguredCredentials($connection): void
    {
        if (! $this->config['bind_dn']) {
            return;
        }

        if (! @ldap_bind($connection, $this->config['bind_dn'], (string) $this->config['bind_password'])) {
            throw DirectoryAuthenticationException::connectionFailed('Não foi possível estabelecer ligação com o servidor LDAP (Bind Inicial Falhou).');
        }
    }

    private function buildUserDn(string $username): string
    {
        return sprintf('uid=%s,%s', $this->escapeDn($username), $this->config['base_dn']);
    }

    private function buildUserFilter(string $username): string
    {
        return sprintf('(uid=%s)', $this->escapeFilter($username));
    }

    private function escapeDn(string $value): string
    {
        if (function_exists('ldap_escape')) {
            return ldap_escape($value, '', LDAP_ESCAPE_DN);
        }

        return addcslashes($value, ',=+<>#;\"');
    }

    private function escapeFilter(string $value): string
    {
        if (function_exists('ldap_escape')) {
            return ldap_escape($value, '', LDAP_ESCAPE_FILTER);
        }

        return preg_replace('/([\x00-\x1F\*\(\)\\\\])/u', '\\\\$1', $value);
    }

    /**
     * @param  \LDAP\Connection|resource  $connection
     */
    private function disconnect($connection): void
    {
        if ($connection instanceof \LDAP\Connection || is_resource($connection)) {
            @ldap_unbind($connection);
        }
    }

    /**
     * Mapeia os dados brutos do LDAP para um array limpo.
     */
    private function mapEntryToUserData(string $username, array $entry): array
    {
        $name = $this->getAttribute($entry, 'displayname')
            ?? $this->getAttribute($entry, 'cn')
            ?? implode(' ', array_filter([
                $this->getAttribute($entry, 'givenname'),
                $this->getAttribute($entry, 'sn'),
            ]))
            ?: $username;

        return [
            'username' => $username,
            'name' => $name,
            'email' => $this->getAttribute($entry, 'mail'),
            'alternative_email' => $this->getAttribute($entry, 'mailforwardingaddress'),
            'cpf' => $this->getAttribute($entry, 'brpersoncpf'),
            'matricula' => $this->getAttribute($entry, 'matgrad'),
            'status' => $this->getAttribute($entry, 'edupersonscopedaffiliation'),
            'is_teacher' => $this->isTeacher($entry),
            'raw' => $entry,
        ];
    }

    /**
     * Verify if the user is a teacher.
     */
    private function isTeacher(array $entry): bool
    {
        if (! isset($entry['edupersonaffiliation']) || ($entry['edupersonaffiliation']['count'] ?? 0) === 0) {
            return false;
        }

        $affiliations = $entry['edupersonaffiliation'];
        $count = $affiliations['count'];

        for ($i = 0; $i < $count; $i++) {
            $value = (string) $affiliations[$i];

            if ($value === '1') {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper to retrieve the first value of an LDAP attribute.
     * Avoids warnings about "Undefined array key" or invalid index access.
     */
    private function getAttribute(array $entry, string $key): ?string
    {
        if (isset($entry[$key]) && ($entry[$key]['count'] ?? 0) > 0) {
            return (string) $entry[$key][0];
        }

        return null;
    }
}
