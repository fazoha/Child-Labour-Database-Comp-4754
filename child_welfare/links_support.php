<?php
require 'config/db.php';
require 'includes/header.php';

// Dropdown data
$ngos = $pdo->query(
    "SELECT ngo_id, ngo_name FROM ngo ORDER BY ngo_name"
)->fetchAll();

$schools = $pdo->query(
    "SELECT school_id, school_name FROM school ORDER BY school_name"
)->fetchAll();

// Add support link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ngo_id      = (int)($_POST['ngo_id'] ?? 0);
    $school_id   = (int)($_POST['school_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $start_date  = trim($_POST['start_date'] ?? '');
    $end_date    = trim($_POST['end_date'] ?? '');

    if ($ngo_id && $school_id && $start_date !== '') {
        $stmt = $pdo->prepare(
          "INSERT INTO support (ngo_id, school_id, description, start_date, end_date)
           VALUES (:ngo_id, :school_id, :description, :start_date, :end_date)"
        );
        $stmt->execute([
            ':ngo_id'      => $ngo_id,
            ':school_id'   => $school_id,
            ':description' => $description !== '' ? $description : null,
            ':start_date'  => $start_date,
            ':end_date'    => $end_date !== '' ? $end_date : null,
        ]);
        $msg = 'Support link added (NGO – School).';
    } else {
        $error = 'NGO, School, and Start Date are required.';
    }
}

// Delete support link
if (isset($_GET['delete_ngo_id'], $_GET['delete_school_id'], $_GET['delete_start_date'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM support
         WHERE ngo_id = :n AND school_id = :s AND start_date = :sd"
    );
    $stmt->execute([
        ':n'  => (int)$_GET['delete_ngo_id'],
        ':s'  => (int)$_GET['delete_school_id'],
        ':sd' => $_GET['delete_start_date'],
    ]);
    $msg = 'Support link deleted.';
}

// Fetch all support links
$links = $pdo->query(
  "SELECT spt.ngo_id, spt.school_id, spt.description, spt.start_date, spt.end_date,
          n.ngo_name, sc.school_name
   FROM support spt
   JOIN ngo    n  ON spt.ngo_id    = n.ngo_id
   JOIN school sc ON spt.school_id = sc.school_id
   ORDER BY n.ngo_name, sc.school_name, spt.start_date"
)->fetchAll();
?>

<h2>NGO – School Support (support)</h2>

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
  <div class="col-md-4">
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
  <div class="col-md-4">
    <label class="form-label">School</label>
    <select name="school_id" class="form-select" required>
      <option value="">-- Select School --</option>
      <?php foreach ($schools as $s): ?>
        <option value="<?= (int)$s['school_id'] ?>">
          <?= htmlspecialchars($s['school_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Support Start Date</label>
    <input type="date" name="start_date" class="form-control" required>
  </div>

  <div class="col-md-8">
    <label class="form-label">Description (optional)</label>
    <textarea name="description" class="form-control" rows="2"></textarea>
  </div>
  <div class="col-md-4">
    <label class="form-label">End Date (optional)</label>
    <input type="date" name="end_date" class="form-control">
  </div>

  <div class="col-12">
    <button class="btn btn-primary">Add Support Link</button>
  </div>
</form>

<table class="table table-sm table-bordered">
  <thead class="table-light">
    <tr>
      <th>NGO</th>
      <th>School</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Description</th>
      <th style="width: 120px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($links as $link): ?>
      <tr>
        <td><?= htmlspecialchars($link['ngo_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($link['school_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($link['start_date'] ?? '') ?></td>
        <td><?= htmlspecialchars($link['end_date'] ?? '') ?></td>
        <td><?= htmlspecialchars($link['description'] ?? '') ?></td>
        <td>
          <a href="?delete_ngo_id=<?= (int)$link['ngo_id'] ?>
                   &delete_school_id=<?= (int)$link['school_id'] ?>
                   &delete_start_date=<?= urlencode($link['start_date']) ?>"
             class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this NGO–School support link?')">
            Delete
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$links): ?>
      <tr>
        <td colspan="6" class="text-center">No support links found yet.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>


<a href="home.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

<?php require 'includes/footer.php'; ?>
