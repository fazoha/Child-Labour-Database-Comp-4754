<?php
require 'config/db.php';
require 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    die('Invalid child id');
}

// Child + school + district
$sql = "SELECT c.*,
               s.school_name, s.school_id,
               d.district_name, d.district_id
        FROM child c
        LEFT JOIN school   s ON c.school_id = s.school_id
        LEFT JOIN district d ON s.district_id = d.district_id
        WHERE c.child_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$child = $stmt->fetch();

if (!$child) {
    die('Child not found');
}

$schoolId   = $child['school_id'] ?? null;
$districtId = $child['district_id'] ?? null;

// Labour links to (child_works_in)
$sqlLab = "SELECT l.*
           FROM child_works_in cw
           JOIN labour l ON cw.labour_id = l.labour_id
           WHERE cw.child_id = :id
           ORDER BY l.labor_type";
$stmtLab = $pdo->prepare($sqlLab);
$stmtLab->execute([':id' => $id]);
$labours = $stmtLab->fetchAll();

// NGOs operating in this district (ngo_operates_in)
$districtNgos = [];
if ($districtId) {
    $sqlNgosDist = "SELECT n.*
                    FROM ngo_operates_in noi
                    JOIN ngo n ON noi.ngo_id = n.ngo_id
                    WHERE noi.district_id = :did
                    ORDER BY n.ngo_name";
    $stmtNgosDist = $pdo->prepare($sqlNgosDist);
    $stmtNgosDist->execute([':did' => $districtId]);
    $districtNgos = $stmtNgosDist->fetchAll();
}

// NGOs supporting this school (support)
$schoolSupport = [];
if ($schoolId) {
    $sqlSupport = "SELECT n.ngo_name,
                          spt.description,
                          spt.start_date,
                          spt.end_date
                   FROM support spt
                   JOIN ngo n ON spt.ngo_id = n.ngo_id
                   WHERE spt.school_id = :sid
                   ORDER BY spt.start_date DESC";
    $stmtSupport = $pdo->prepare($sqlSupport);
    $stmtSupport->execute([':sid' => $schoolId]);
    $schoolSupport = $stmtSupport->fetchAll();
}
?>

<h2>Child Details</h2>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title"><?= htmlspecialchars($child['child_name']) ?></h5>
    <p class="card-text mb-1">
      <strong>Age:</strong> <?= htmlspecialchars($child['age']) ?> |
      <strong>Gender:</strong> <?= htmlspecialchars($child['gender']) ?> |
      <strong>Grade:</strong> <?= htmlspecialchars($child['grade_level']) ?>
    </p>
    <p class="card-text mb-1">
      <strong>Parental Status:</strong> <?= htmlspecialchars($child['parental_status']) ?>
    </p>
    <p class="card-text mb-0">
      <strong>School:</strong> <?= htmlspecialchars($child['school_name'] ?? 'N/A') ?><br>
      <strong>District:</strong> <?= htmlspecialchars($child['district_name'] ?? 'N/A') ?>
    </p>
  </div>
</div>

<h4>Labour Involvement (child_works_in)</h4>
<?php if (!$labours): ?>
  <p>No labour records for this child.</p>
<?php else: ?>
  <table class="table table-sm table-bordered">
    <thead class="table-light">
      <tr>
        <th>Labor Type</th>
        <th>Site Type</th>
        <th>Hours / Week</th>
        <th>Wage Amount</th>
        <th>Wage Period</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($labours as $l): ?>
        <tr>
          <td><?= htmlspecialchars($l['labor_type']) ?></td>
          <td><?= htmlspecialchars($l['site_type']) ?></td>
          <td><?= htmlspecialchars($l['typical_hours_per_week']) ?></td>
          <td><?= htmlspecialchars($l['typical_wage_amount']) ?></td>
          <td><?= htmlspecialchars($l['wage_period']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<h4>NGOs Operating in Child's District (ngo_operates_in)</h4>
<?php if (!$districtId || !$districtNgos): ?>
  <p>No NGOs recorded for this district.</p>
<?php else: ?>
  <table class="table table-sm table-bordered">
    <thead class="table-light">
      <tr>
        <th>NGO Name</th>
        <th>Type of Service</th>
        <th>NGO Type</th>
        <th>Capacity</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($districtNgos as $n): ?>
        <tr>
          <td><?= htmlspecialchars($n['ngo_name']) ?></td>
          <td><?= htmlspecialchars($n['type_service']) ?></td>
          <td><?= htmlspecialchars($n['ngo_type']) ?></td>
          <td><?= htmlspecialchars($n['capacity']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<h4>NGOs Supporting Child's School (support)</h4>
<?php if (!$schoolId || !$schoolSupport): ?>
  <p>No support records for this school.</p>
<?php else: ?>
  <table class="table table-sm table-bordered">
    <thead class="table-light">
      <tr>
        <th>NGO Name</th>
        <th>Description</th>
        <th>Start Date</th>
        <th>End Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($schoolSupport as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['ngo_name']) ?></td>
          <td><?= htmlspecialchars($s['description']) ?></td>
          <td><?= htmlspecialchars($s['start_date']) ?></td>
          <td><?= htmlspecialchars($s['end_date']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<a href="index.php?table=child" class="btn btn-secondary mt-3">Back to Children</a>

<?php require 'includes/footer.php'; ?>
