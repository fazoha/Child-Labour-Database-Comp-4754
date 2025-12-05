<?php
// sql_console.php
require 'config/db.php';

$resultRows = [];
$columns    = [];
$errorMsg   = '';
$infoMsg    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = trim($_POST['sql'] ?? '');

    if ($sql !== '') {
        try {
            $stmt = $pdo->query($sql);

            if ($stmt->columnCount() > 0) {
                $resultRows = $stmt->fetchAll();
                $columns    = array_keys($resultRows[0] ?? []);
                $infoMsg    = 'Query executed successfully. Rows returned: ' . count($resultRows);
            } else {
                $infoMsg = 'Query executed successfully. Rows affected: ' . $stmt->rowCount();
            }
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
        }
    } else {
        $errorMsg = 'Please type a SQL query.';
    }
}

require 'includes/header.php';
?>

<h1 class="mb-4">SQL Query Console</h1>

<div class="row g-3 mb-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">Write SQL Query</h5>

        <?php if ($errorMsg): ?>
          <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errorMsg) ?>
          </div>
        <?php endif; ?>

        <?php if ($infoMsg && !$errorMsg): ?>
          <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($infoMsg) ?>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label for="sql" class="form-label">SQL</label>
            <textarea
              class="form-control"
              id="sql"
              name="sql"
              rows="5"
              placeholder="Example: SELECT * FROM child;"
            ><?= isset($_POST['sql']) ? htmlspecialchars($_POST['sql']) : '' ?></textarea>
          </div>

          <button type="submit" class="btn btn-primary">
            Run Query
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if ($columns && $resultRows): ?>
  <div class="row g-3 mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Result</h5>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm">
              <thead class="table-light">
                <tr>
                  <?php foreach ($columns as $col): ?>
                    <th><?= htmlspecialchars($col) ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($resultRows as $row): ?>
                  <tr>
                    <?php foreach ($columns as $col): ?>
                      <td><?= htmlspecialchars((string)$row[$col]) ?></td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<a href="home.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
<?php
require 'includes/footer.php';
