<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Este projeto é uma aplicação Blade "monolítica": as próprias telas
    | (mesma origem) chamam os endpoints JSON via fetch(), autenticadas por
    | cookie de sessão + CSRF — nunca por um front-end separado rodando em
    | outro domínio/porta. Por isso NENHUMA origem externa precisa (nem
    | deveria) ser autorizada por padrão: sem este arquivo, o middleware de
    | CORS do framework cairia nos valores internos dele mesmo (que
    | costumam liberar "allowed_origins => ['*']" para os paths abaixo) sem
    | nenhuma auditoria explícita. Publicando o arquivo, o comportamento
    | fica documentado e controlado aqui.
    |
    | Caso um front-end separado (SPA/app mobile) precise consumir a API no
    | futuro, adicione o(s) domínio(s) confiável(is) em
    | "allowed_origins"/"allowed_origins_patterns" — nunca "*" combinado
    | com "supports_credentials => true", o que o navegador recusa por
    | design (e exporia cookies de sessão a qualquer origem).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Ambiente local de ensino: qualquer origem pode chamar a API (o app
    // Flutter web funciona com qualquer IP/porta, sem precisar liberar o IP
    // da máquina a cada troca de rede). A API autentica por token Bearer,
    // não por cookie, então '*' com supports_credentials=false é válido.
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
