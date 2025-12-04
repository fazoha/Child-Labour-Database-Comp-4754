<?php
require 'config/db.php';
require 'tables_config.php';
require 'includes/header.php';

// Get which table need to edit/create from
$table = $_GET['table'] ?? '';
if (!isset($MAIN_TABLES[$table])) {
    die('Invalid table');
}

// Load config 
$cfg = $MAIN_TABLES[$table];
$pk  = $cfg['pk'];

// Get ID from URL
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mode = $id > 0 ? 'edit' : 'create';

$data = [];
foreach ($cfg['fields'] as $name => $_) {
    $data[$name] = '';
}

// Load existing row
if ($mode === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE {$pk} = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        die('Record not found');
    }
    foreach ($data as $name => $_) {
        $data[$name] = $row[$name] ?? '';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $name => $_) {
        $data[$name] = trim($_POST[$name] ?? '');
    }

    // Basic required validation
    $errors = [];
    foreach ($cfg['fields'] as $name => $meta) {
        if (!empty($meta['required']) && $data[$name] === '') {
            $errors[] = $meta['label'] . ' is required.';
        }
    }
    //  save to database
    if (empty($errors)) {
        if ($mode === 'create') {
            $cols = array_keys($data);
            $placeholders = array_map(fn($c) => ':' . $c, $cols);
            $sql = "INSERT INTO {$table} (" . implode(',', $cols) . ")
                    VALUES (" . implode(',', $placeholders) . ")";
        } else {
            $setParts = [];
            foreach ($data as $col => $_v) {
                $setParts[] = "$col = :$col";
            }
            $sql = "UPDATE {$table} SET " . implode(',', $setParts) . " WHERE {$pk} = :id";
        }

        $stmt = $pdo->prepare($sql);
        foreach ($data as $col => $value) {
            $stmt->bindValue(':' . $col, $value === '' ? null : $value);
        }
        if ($mode === 'edit') {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        try {
            $stmt->execute();
            header("Location: index.php?table={$table}");
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<h2><?= ucfirst($mode) . ' ' . htmlspecialchars(rtrim($cfg['label'], 's')) ?></h2>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="row g-3">
  <?php foreach ($cfg['fields'] as $name => $meta): ?>
    <div class="col-md-6">
      <label class="form-label"><?= htmlspecialchars($meta['label']) ?></label>
      <?php if (($meta['type'] ?? '') === 'fk'): ?>
        <?php
          $sqlFk = "SELECT {$meta['ref_pk']} AS id, {$meta['ref_label']} AS label FROM {$meta['ref_table']} ORDER BY label";
          $stmtFk = $pdo->query($sqlFk);
          $options = $stmtFk->fetchAll();
        ?>
        <select name="<?= htmlspecialchars($name) ?>" class="form-select">
          <option value="">-- Select --</option>
          <?php foreach ($options as $opt): ?>
            <option value="<?= (int)$opt['id'] ?>"
              <?= $data[$name] == $opt['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($opt['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      <?php else: ?>
        <input
          type="<?= htmlspecialchars($meta['type'] ?? 'text') ?>"
          name="<?= htmlspecialchars($name) ?>"
          value="<?= htmlspecialchars($data[$name]) ?>"
          class="form-control"
          <?= !empty($meta['step']) ? 'step="'.$meta['step'].'"' : '' ?>
          <?= !empty($meta['maxlength']) ? 'maxlength="'.$meta['maxlength'].'"' : '' ?>
        >
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <div class="col-12">
    <button class="btn btn-primary">Save</button>
    <a href="index.php?table=<?= htmlspecialchars($table) ?>" class="btn btn-secondary">Cancel</a>
  </div>
</form>

<?php require 'includes/footer.php'; ?>
