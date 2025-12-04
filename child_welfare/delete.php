<?php
require 'config/db.php';
require 'tables_config.php';

// Get table name 
$table = $_GET['table'] ?? '';
$id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id || !isset($MAIN_TABLES[$table])) {
    die('Invalid request');
}

// Get the primary key column name
$pk = $MAIN_TABLES[$table]['pk'];

try {
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE {$pk} = :id");
    $stmt->execute([':id' => $id]);

    $msg = 'Record deleted successfully.';
    header('Location: index.php?table=' . urlencode($table) . '&msg=' . urlencode($msg));
    exit;

} catch (PDOException $e) {
    $errorMsg = 'This record cannot be deleted because it is used in related tables. '
              . 'Delete or update related records first.';
    header('Location: index.php?table=' . urlencode($table) . '&error=' . urlencode($errorMsg));
    exit;
}
