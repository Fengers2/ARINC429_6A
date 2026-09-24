<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
$primary = primaryColumn(tableColumns());
$statement = db()->prepare('DELETE FROM ' . identifier('instrumentos2') . ' WHERE ' . identifier($primary['COLUMN_NAME']) . ' = :id');
$statement->execute(['id' => (string) ($_POST['id'] ?? '')]);
header('Location: index.php?mensaje=' . urlencode('Instrumento eliminado correctamente.')); exit;