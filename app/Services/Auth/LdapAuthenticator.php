<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\DirectoryAuthenticationException;

use function array_change_key_case;
use function is_resource;
use function ldap_connect;
use function ldap_get_entries;
use function ldap_search;
use function ldap_set_option;
use function ldap_unbind;

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
     *     username:string,
     *     name:?string,
     *     email:?string,
     *     alternative_email:?string,
     *     cpf:?string,
     *     matricula:?string,
     *     status:?string,
     *     raw:array<string,mixed>
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
                ],
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
            throw DirectoryAuthenticationException::connectionFailed('Não foi possível estabelecer ligação com o servidor LDAP.');
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
     * @param  array<string, mixed>  $entry
     * @return array{
     *     username:string,
     *     name:?string,
     *     email:?string,
     *     alternative_email:?string,
     *     cpf:?string,
     *     matricula:?string,
     *     status:?string,
     *     raw:array<string,mixed>
     * }
     */
    private function mapEntryToUserData(string $username, array $entry): array
    {
        $name = $entry['displayname'][0]
            ?? $entry['cn'][0]
            ?? implode(' ', array_filter([$entry['givenname'][0] ?? null, $entry['sn'][0] ?? null]))
            ?: null;

        $email = $entry['mail'][0] ?? null;
        $alternativeEmail = $entry['mailforwardingaddress'][0] ?? null;
        $cpf = $entry['brpersoncpf'][0] ?? null;
        $matricula = $entry['matgrad'][0] ?? null;
        $status = $entry['edupersonscopedaffiliation'][0] ?? null;

        return [
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'alternative_email' => $alternativeEmail,
            'cpf' => $cpf,
            'matricula' => $matricula,
            'status' => $status,
            'raw' => $entry,
        ];
    }
}
