<?php

namespace App\Exceptions\Auth;

use RuntimeException;

class DirectoryAuthenticationException extends RuntimeException
{
    public function __construct(
        public readonly string $reason,
        string $message,
        ?int $code = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code ?? 0, $previous);
    }

    public static function connectionFailed(?string $message = null, ?\Throwable $previous = null): self
    {
        return new self('connection', $message ?? 'Não foi possível conectar ao servidor LDAP.', previous: $previous);
    }

    public static function invalidCredentials(): self
    {
        return new self('invalid_credentials', 'Credenciais inválidas.');
    }

    public static function userNotFound(): self
    {
        return new self('user_not_found', 'Usuário não encontrado no diretório.');
    }

    public static function searchFailed(): self
    {
        return new self('search_failed', 'Falha ao recuperar informações do diretório.');
    }
}
