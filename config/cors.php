<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | Define qué rutas deben permitir solicitudes CORS.
    |
    */
    'paths' => ['api/*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Qué métodos HTTP están permitidos. ['*'] significa todos.
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Desde qué orígenes se permiten las solicitudes.
    | Añade aquí la URL de tu front en local y producción.
    |
    */
    'allowed_origins' => [
        'http://localhost:5173', // frontend local
        'https://proyecto-el-impostor-frontend.vercel.app', // frontend en Vercel
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origin Patterns
    |--------------------------------------------------------------------------
    |
    | Puedes usar patrones de expresiones regulares para permitir múltiples orígenes dinámicos.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Qué headers están permitidos. ['*'] significa todos.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Qué headers se exponen al cliente.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Cuánto tiempo (en segundos) el navegador puede cachear la respuesta CORS.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Si se permite enviar cookies o headers de autenticación.
    |
    */
    'supports_credentials' => false,

];