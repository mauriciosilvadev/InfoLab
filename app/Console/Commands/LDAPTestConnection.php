<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class LDAPTestConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ldap-test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the LDAP connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = (string) config('ufes_ldap.host');
        $port = (int) config('ufes_ldap.port', 389);
        $baseDn = (string) config('ufes_ldap.base_dn');

        $this->info("Testando conexão com LDAP em {$host}:{$port}");

        $username = $this->ask('Informe o username UFES (sem domínio)');
        $password = $this->secret('Informe a senha UFES');

        if (! $username || ! $password) {
            $this->error('Username e senha são obrigatórios.');

            return self::FAILURE;
        }

        $connection = @ldap_connect($host, $port);

        if (! $connection) {
            $this->error("Falha ao conectar em {$host}:{$port}.");

            return self::FAILURE;
        }

        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

        $userDn = sprintf('uid=%s,%s', $username, $baseDn);
        $this->line("Tentando bind com DN: {$userDn}");

        if (! @ldap_bind($connection, $userDn, $password)) {
            $errorCode = ldap_errno($connection);
            $errorMessage = ldap_error($connection);
            @ldap_unbind($connection);

            $this->error("ldap_bind falhou ({$errorCode}): {$errorMessage}");

            return self::FAILURE;
        }

        $this->info('Conexão e bind realizados com sucesso.');

        $search = @ldap_search(
            $connection,
            $baseDn,
            sprintf('(uid=%s)', $username),
        );

        if ($search === false) {
            @ldap_unbind($connection);
            $this->error('Pesquisa LDAP falhou após o bind bem-sucedido.');

            return self::FAILURE;
        }

        $entries = ldap_get_entries($connection, $search);

        if (! $entries || ($entries['count'] ?? 0) === 0) {
            $this->warn('Bind OK, porém nenhum registro foi retornado pela busca.');

            return self::SUCCESS;
        }

        $payload = [
            'queried_at' => now()->toIso8601String(),
            'host' => $host,
            'base_dn' => $baseDn,
            'filter' => sprintf('(uid=%s)', $username),
            'entries' => $entries,
        ];

        $fileName = sprintf(
            'ldap/ufes_%s_%s.json',
            str_replace(['@', '.', ' '], '_', $username),
            now()->format('Ymd_His')
        );

        Storage::disk('local')->put(
            $fileName,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        @ldap_unbind($connection);

        $this->info('Resposta LDAP armazenada com sucesso.');
        $this->line('Arquivo gerado: ' . storage_path('app/' . $fileName));

        return self::SUCCESS;
    }
}
