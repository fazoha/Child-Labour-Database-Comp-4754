<?php
require 'config/db.php';
require 'includes/header.php';

// Dropdown data
$ngos = $pdo->query(
    "SELECT ngo_id, ngo_name FROM ngo ORDER BY ngo_name"
)->fetchAll();

$districts = $pdo->query(
    "SELECT district_id, district_name FROM district ORDER BY district_name"
)->fetchAll();

// Add link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ngo_id      = (int)($_POST['ngo_id'] ?? 0);
    $district_id = (int)($_POST['district_id'] ?? 0);

    if ($ngo_id && $district_id) {
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO ngo_operates_in (ngo_id, district_id)
             VALUES (:ngo_id, :district_id)"
        );
        $stmt->execute([
            ':ngo_id'      => $ngo_id,
            ':district_id' => $district_id,
        ]);
        $msg = 'Link added (NGO – District).';
    } else {
        $error = 'Please select both an NGO and a district.';
    }
}

// Delete link
if (isset($_GET['delete_ngo_id'], $_GET['delete_district_id'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM ngo_operates_in
         WHERE ngo_id = :n AND district_id = :d"
    );
    $stmt->execute([
        ':n' => (int)$_GET['delete_ngo_id'],
        ':d' => (int)$_GET['delete_district_id'],
    ]);
    $msg = 'Link deleted.';
}

// Fetch links
$links = $pdo->query(
  "SELECT noi.ngo_id, noi.district_id,
          n.ngo_name, d.district_name
   FROM ngo_operates_in noi
   JOIN ngo      n ON noi.ngo_id      = n.ngo_id
   JOIN district d ON noi.district_id = d.district_id
   ORDER BY n.ngo_name, d.district_name"
)->fetchAll();
?>

<h2>NGO – District Links (ngo_operates_in)</h2>

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
    <label class="form-label">NGO</label>
    <select name="ngo_id" class="form-select" required>
      <option value="">-- Select NGO --</option>
      <?php foreach ($ngos as $n): ?>
        <option value="<?= (int)$n['ngo_id'] ?>">
          <?= htmlspecialchars($n['ngo_name']) ?>
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
      <th>NGO</th>
      <th>District</th>
      <th style="width: 120px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($links as $link): ?>
      <tr>
        <td><?= htmlspecialchars($link['ngo_name']) ?></td>
        <td><?= htmlspecialchars($link['district_name']) ?></td>
        <td>
          <a href="?delete_ngo_id=<?= (int)$link['ngo_id'] ?>&delete_district_id=<?= (int)$link['district_id'] ?>"
             class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this NGO–District link?')">
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
