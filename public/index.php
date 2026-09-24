<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
$error = null; $columns = []; $rows = []; $primary = null;
try {
    $columns = tableColumns();
    $primary = primaryColumn($columns);
    $rows = db()->query('SELECT * FROM ' . identifier('instrumentos2') . ' ORDER BY ' . identifier($primary['COLUMN_NAME']) . ' DESC')->fetchAll();
} catch (Throwable $exception) { $error = $exception->getMessage(); }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Instrumentos | ARINC_429_6A</title><link rel="stylesheet" href="styles.css"></head><body><main class="shell">
<header class="topbar"><div><p class="eyebrow">ARINC_429_6A · instrumentos2</p><h1>Instrumentos</h1><p class="subtitle">Gestiona los registros de la tabla <strong>instrumentos2</strong>.</p></div><?php if ($error === null): ?><a class="button button-primary" href="crear.php">+ Nuevo instrumento</a><?php endif; ?></header>
<?php if ($error !== null): ?><div class="alert alert-error">No se pudo cargar la tabla: <?= e($error) ?></div><?php elseif (isset($_GET['mensaje'])): ?><div class="alert alert-success"><?= e($_GET['mensaje']) ?></div><?php else: ?><section class="panel"><div class="panel-heading"><p class="eyebrow">Registros actuales</p><h2><?= count($rows) ?> instrumento<?= count($rows) === 1 ? '' : 's' ?></h2></div><div class="table-wrap"><table><thead><tr><?php foreach ($columns as $column): ?><th><?= e($column['COLUMN_NAME']) ?></th><?php endforeach; ?><th>Acciones</th></tr></thead><tbody>
<?php if ($rows === []): ?><tr><td class="empty" colspan="<?= count($columns) + 1 ?>">Aún no hay instrumentos registrados.</td></tr><?php else: foreach ($rows as $row): ?><tr><?php foreach ($columns as $column): ?><td title="<?= e((string) ($row[$column['COLUMN_NAME']] ?? '')) ?>"><?= e(displayValue($row[$column['COLUMN_NAME']] ?? null)) ?></td><?php endforeach; ?><td class="actions"><a class="button button-small" href="editar.php?id=<?= urlencode((string) $row[$primary['COLUMN_NAME']]) ?>">Editar</a><form method="post" action="eliminar.php" onsubmit="return confirm('¿Eliminar este instrumento?');"><input type="hidden" name="id" value="<?= e((string) $row[$primary['COLUMN_NAME']]) ?>"><button class="button button-small button-danger" type="submit">Eliminar</button></form></td></tr><?php endforeach; endif; ?>
</tbody></table></div></section><?php endif; ?></main></body></html>