<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UFES LDAP Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the connection to the UFES LDAP directory. All values can be
    | overridden through the environment file.
    |
    */

    'host' => env('UFES_LDAP_HOST', 'ldap1b.ufes.br'),

    'port' => (int) env('UFES_LDAP_PORT', 389),

    'base_dn' => env('UFES_LDAP_BASE_DN', 'ou=People,dc=ufes,dc=br'),

    /*
    |--------------------------------------------------------------------------
    | Optional Bind Credentials
    |--------------------------------------------------------------------------
    |
    | If the LDAP server requires binding before authentication attempts,
    | provide the DN and password here. Leave null to perform anonymous binds.
    |
    */

    'bind_dn' => env('UFES_LDAP_BIND_DN'),

    'bind_password' => env('UFES_LDAP_BIND_PASSWORD'),
];
