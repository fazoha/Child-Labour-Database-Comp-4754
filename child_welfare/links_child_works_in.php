<?php
// links_child_works_in.php
require 'config/db.php';
require 'includes/header.php';

// Fetch dropdown data
$children = $pdo->query(
    "SELECT child_id, child_name FROM child ORDER BY child_name"
)->fetchAll();

$labours = $pdo->query(
    "SELECT labour_id, labor_type FROM labour ORDER BY labor_type"
)->fetchAll();

// Handle add link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id  = (int)($_POST['child_id'] ?? 0);
    $labour_id = (int)($_POST['labour_id'] ?? 0);

    if ($child_id && $labour_id) {
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO child_works_in (child_id, labour_id)
             VALUES (:child_id, :labour_id)"
        );
        $stmt->execute([
            ':child_id'  => $child_id,
            ':labour_id' => $labour_id,
        ]);
        $msg = 'Link added (Child – Labour).';
    } else {
        $error = 'Please select both a child and a labour type.';
    }
}

// Handle delete link
if (isset($_GET['delete_child_id'], $_GET['delete_labour_id'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM child_works_in
         WHERE child_id = :c AND labour_id = :l"
    );
    $stmt->execute([
        ':c' => (int)$_GET['delete_child_id'],
        ':l' => (int)$_GET['delete_labour_id'],
    ]);
    $msg = 'Link deleted.';
}

// Load all links
$links = $pdo->query(
  "SELECT cwi.child_id, cwi.labour_id,
          c.child_name, l.labor_type
   FROM child_works_in cwi
   JOIN child  c ON cwi.child_id  = c.child_id
   JOIN labour l ON cwi.labour_id = l.labour_id
   ORDER BY c.child_name, l.labor_type"
)->fetchAll();
?>

<h2>Child – Labour Links (child_works_in)</h2>

<?php if (!empty($msg)): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<form method="post" class="row g-3 mb-4">
  <div class="col-md-5">
    <label class="form-label">Child</label>
    <select name="child_id" class="form-select" required>
      <option value="">-- Select Child --</option>
      <?php foreach ($children as $c): ?>
        <option value="<?= (int)$c['child_id'] ?>">
          <?= htmlspecialchars($c['child_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-5">
    <label class="form-label">Labour Type</label>
    <select name="labour_id" class="form-select" required>
      <option value="">-- Select Labour Type --</option>
      <?php foreach ($labours as $l): ?>
        <option value="<?= (int)$l['labour_id'] ?>">
          <?= htmlspecialchars($l['labor_type']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <button class="btn btn-primary w-100">Add Link</button>
  </div>
</form>

<table class="table table-bordered table-sm">
  <thead class="table-light">
    <tr>
      <th>Child</th>
      <th>Labour Type</th>
      <th style="width: 120px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($links as $link): ?>
      <tr>
        <td><?= htmlspecialchars($link['child_name']) ?></td>
        <td><?= htmlspecialchars($link['labor_type']) ?></td>
        <td>
          <a href="?delete_child_id=<?= (int)$link['child_id'] ?>&delete_labour_id=<?= (int)$link['labour_id'] ?>"
             class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this Child–Labour link?')">
            Delete
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$links): ?>
      <tr>
        <td colspan="3" class="text-center">No links found yet.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<a href="home.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

<?php require 'includes/footer.php'; ?>
