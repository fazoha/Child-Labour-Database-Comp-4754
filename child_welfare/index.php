<?php
require 'config/db.php';
require 'tables_config.php';
require 'includes/header.php';


// Which table to show
$table = $_GET['table'] ?? 'child';
if (!isset($MAIN_TABLES[$table])) {
    $table = 'child';
}

$cfg = $MAIN_TABLES[$table];
$q   = trim($_GET['q'] ?? '');  // Search query from the URL

// messages from redirects 
$msg   = $_GET['msg']   ?? '';
$error = $_GET['error'] ?? '';

$where  = '';
$params = [];


//dynamic search
if ($q !== '' && !empty($cfg['search_columns'])) {
    $likeParts = [];
    foreach ($cfg['search_columns'] as $col) {
        $likeParts[] = "$col LIKE :q";
    }
    $where = 'WHERE ' . implode(' OR ', $likeParts);
    $params[':q'] = '%' . $q . '%';
}

// Final SQL query
$sql = "SELECT * FROM {$table} $where ORDER BY {$cfg['pk']} DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2><?= htmlspecialchars($cfg['label']) ?></h2>
</div>

<?php if ($msg): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<form class="row g-2 mb-3" method="get">
  <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
  <div class="col-auto">
    <input type="text" name="q" class="form-control"
           placeholder="Search..." value="<?= htmlspecialchars($q) ?>">
  </div>
  <div class="col-auto">
    <button class="btn btn-primary">Search</button>
    <a href="index.php?table=<?= htmlspecialchars($table) ?>" class="btn btn-secondary">Reset</a>
  </div>
</form>

<a href="edit.php?table=<?= htmlspecialchars($table) ?>" class="btn btn-success mb-3">
  Create <?= htmlspecialchars(rtrim($cfg['label'], 's')) ?>
</a>

<table class="table table-bordered table-striped table-sm">
  <thead class="table-light">
    <tr>
      <th><?= htmlspecialchars($cfg['pk']) ?></th>
      <?php foreach ($cfg['fields'] as $name => $meta): ?>
        <th><?= htmlspecialchars($meta['label']) ?></th>
      <?php endforeach; ?>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row[$cfg['pk']]) ?></td>
      <?php foreach ($cfg['fields'] as $name => $meta): ?>
        <td><?= htmlspecialchars($row[$name] ?? '') ?></td>
      <?php endforeach; ?>
      <td class="text-nowrap">
        <a href="edit.php?table=<?= htmlspecialchars($table) ?>&id=<?= (int)$row[$cfg['pk']] ?>"
           class="btn btn-sm btn-primary">Edit</a>
        <a href="delete.php?table=<?= htmlspecialchars($table) ?>&id=<?= (int)$row[$cfg['pk']] ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('Delete this record?')">Delete</a>
        <?php if ($table === 'child'): ?>
          <a href="view_child.php?id=<?= (int)$row['child_id'] ?>"
             class="btn btn-sm btn-outline-secondary">Details</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<a href="home.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

<?php require 'includes/footer.php'; ?>
