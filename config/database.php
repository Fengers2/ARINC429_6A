<?php

declare(strict_types=1);

function db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('ARINC_DB_HOST') ?: '127.0.0.1';
    $name = getenv('ARINC_DB_NAME') ?: 'ARINC_429_6A';
    $user = getenv('ARINC_DB_USER') ?: 'root';
    $password = getenv('ARINC_DB_PASS') ?: '';
    $connection = new PDO('mysql:host=' . $host . ';dbname=' . $name . ';charset=utf8mb4', $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $connection;
}

function tableColumns(): array
{
    $statement = db()->prepare(
        'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_KEY '
        . 'FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table '
        . 'ORDER BY ORDINAL_POSITION'
    );
    $statement->execute([
        'schema' => getenv('ARINC_DB_NAME') ?: 'ARINC_429_6A',
        'table' => 'instrumentos2',
    ]);
    $columns = $statement->fetchAll();
    if ($columns === []) {
        throw new RuntimeException('La tabla instrumentos2 no existe o no tiene columnas visibles.');
    }
    return $columns;
}

function primaryColumn(array $columns): array
{
    foreach ($columns as $column) {
        if ($column['COLUMN_KEY'] === 'PRI') {
            return $column;
        }
    }
    throw new RuntimeException('La tabla instrumentos2 necesita una clave primaria.');
}

function identifier(string $value): string
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $value)) {
        throw new InvalidArgumentException('Identificador SQL no válido.');
    }
    return '`' . $value . '`';
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function inputType(array $column): string
{
    return match ($column['DATA_TYPE']) {
        'date' => 'date',
        'datetime', 'timestamp' => 'datetime-local',
        'time' => 'time',
        'int', 'integer', 'bigint', 'smallint', 'mediumint', 'tinyint', 'decimal', 'float', 'double' => 'number',
        default => 'text',
    };
}

function displayValue(mixed $value): string
{
    if ($value === null) {
        return 'NULL';
    }
    $text = (string) $value;
    return strlen($text) > 80 ? substr($text, 0, 77) . '...' : $text;
}