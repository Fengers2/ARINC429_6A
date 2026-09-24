<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
$columns = tableColumns();
$primary = primaryColumn($columns);
$editable = array_values(array_filter($columns, static fn (array $column): bool => $column['EXTRA'] !== 'auto_increment'));
$values = []; $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($editable as $column) { $name = $column['COLUMN_NAME']; $values[$name] = trim((string) ($_POST[$name] ?? '')); }
    $required = array_filter($editable, static fn (array $column): bool => $column['IS_NULLABLE'] === 'NO' && $column['COLUMN_DEFAULT'] === null && $column['EXTRA'] === '');
    if (array_filter($required, static fn (array $column): bool => $values[$column['COLUMN_NAME']] === '')) { $error = 'Completa todos los campos obligatorios.'; }
    else { try { $names = array_map(static fn (array $column): string => $column['COLUMN_NAME'], $editable); $sql = 'INSERT INTO ' . identifier('instrumentos2') . ' (' . implode(', ', array_map('identifier', $names)) . ') VALUES (' . implode(', ', array_map(static fn (string $name): string => ':' . $name, $names)) . ')'; $statement = db()->prepare($sql); foreach ($values as $name => $value) { $statement->bindValue(':' . $name, $value === '' ? null : $value); } $statement->execute(); header('Location: index.php?mensaje=' . urlencode('Instrumento creado correctamente.')); exit; } catch (Throwable $exception) { $error = 'No se pudo crear el instrumento: ' . $exception->getMessage(); } }
}
function renderField(array $column, mixed $value): string { $name = $column['COLUMN_NAME']; $required = $column['IS_NULLABLE'] === 'NO' && $column['COLUMN_DEFAULT'] === null; return '<label for="' . e($name) . '">' . e($name) . ($required ? ' *' : '') . '</label><input id="' . e($name) . '" name="' . e($name) . '" type="' . e(inputType($column)) . '" value="' . e($value) . '"' . ($required ? ' required' : '') . '>'; }
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Nuevo instrumento</title><link rel="stylesheet" href="styles.css"></head><body><main class="shell shell-narrow"><a class="back-link" href="index.php">← Volver a instrumentos</a><section class="form-panel"><p class="eyebrow">Crear registro</p><h1>Nuevo instrumento</h1><?php if ($error !== null): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?><form method="post" class="user-form"><?php foreach ($editable as $column): echo renderField($column, $values[$column['COLUMN_NAME']] ?? ''); endforeach; ?><div class="form-actions"><a class="button" href="index.php">Cancelar</a><button class="button button-primary" type="submit">Guardar instrumento</button></div></form></section></main></body></html>