<?php
require 'config/db.php';
require 'includes/header.php';

// Dropdown data
$crises = $pdo->query(
    "SELECT crisis_id, crisis_name FROM local_crisis ORDER BY crisis_name"
)->fetchAll();

$districts = $pdo->query(
    "SELECT district_id, district_name FROM district ORDER BY district_name"
)->fetchAll();

// Add link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crisis_id   = (int)($_POST['crisis_id'] ?? 0);
    $district_id = (int)($_POST['district_id'] ?? 0);

    if ($crisis_id && $district_id) {
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO crisis_effects (crisis_id, district_id)
             VALUES (:crisis_id, :district_id)"
        );
        $stmt->execute([
            ':crisis_id'   => $crisis_id,
            ':district_id' => $district_id,
        ]);
        $msg = 'Link added (Crisis – District).';
    } else {
        $error = 'Please select both a crisis and a district.';
    }
}

// Delete link
if (isset($_GET['delete_crisis_id'], $_GET['delete_district_id'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM crisis_effects
         WHERE crisis_id = :c AND district_id = :d"
    );
    $stmt->execute([
        ':c' => (int)$_GET['delete_crisis_id'],
        ':d' => (int)$_GET['delete_district_id'],
    ]);
    $msg = 'Link deleted.';
}

// Fetch links
$links = $pdo->query(
  "SELECT ce.crisis_id, ce.district_id,
          lc.crisis_name, d.district_name
   FROM crisis_effects ce
   JOIN local_crisis lc ON ce.crisis_id   = lc.crisis_id
   JOIN district     d ON ce.district_id = d.district_id
   ORDER BY lc.crisis_name, d.district_name"
)->fetchAll();
?>

<h2>Crisis – District Links (crisis_effects)</h2>

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
    <label class="form-label">Crisis</label>
    <select name="crisis_id" class="form-select" required>
      <option value="">-- Select Crisis --</option>
      <?php foreach ($crises as $c): ?>
        <option value="<?= (int)$c['crisis_id'] ?>">
          <?= htmlspecialchars($c['crisis_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-5">
    <label class="form-label">District</label>
    <select name="district_id" class="form-select" required>
      <option value="">-- Select District --</option>
      <?php foreach ($districts as $d): ?>
        <option value="<?= (int)$d['district_id'] ?>">
          <?= htmlspecialchars($d['district_name']) ?>
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
      <th>Crisis</th>
      <th>District</th>
      <th style="width: 120px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($links as $link): ?>
      <tr>
        <td><?= htmlspecialchars($link['crisis_name']) ?></td>
        <td><?= htmlspecialchars($link['district_name']) ?></td>
        <td>
          <a href="?delete_crisis_id=<?= (int)$link['crisis_id'] ?>&delete_district_id=<?= (int)$link['district_id'] ?>"
             class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this Crisis–District link?')">
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
