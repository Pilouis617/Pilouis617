<?php
declare(strict_types=1);

// Valeurs locales XAMPP. Les variables d'environnement prennent la priorité.
return [
    'host' => getenv('FUTUREBOX_DB_HOST') ?: '127.0.0.1',
    'port' => getenv('FUTUREBOX_DB_PORT') ?: '3306',
    'database' => getenv('FUTUREBOX_DB_NAME') ?: 'futurebox',
    'user' => getenv('FUTUREBOX_DB_USER') ?: 'root',
    'password' => getenv('FUTUREBOX_DB_PASSWORD') ?: '',
    'timezone' => 'Europe/Paris',
];
