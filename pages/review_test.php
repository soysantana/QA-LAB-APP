<?php
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(1);

$id = $_GET["id"] ?? "";
$type = $_GET["type"] ?? "";

// Load essay data from ANY table using Tracking ID
$tbl = find_by_sql("SELECT * FROM test_source_map WHERE code='{$type}' LIMIT 1");

if (empty($tbl)) die("Unknown test type");

$tableName = $tbl[0]["table_name"];

$entry = find_by_sql("SELECT * FROM {$tableName} WHERE id='{$id}' LIMIT 1");
if (empty($entry)) die("Record not found");

$data = $entry[0];

include_once('../components/header.php');
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Essay Review</h1>
  </div>

  <section class="section">
    <form action="review_save.php" method="POST" enctype="multipart/form-data">

      <input type="hidden" name="table" value="<?= $tableName ?>">
      <input type="hidden" name="id" value="<?= $id ?>">
      <input type="hidden" name="Sample_ID" value="<?= $data["Sample_ID"] ?>">
      <input type="hidden" name="Sample_Number" value="<?= $data["Sample_Number"] ?>">
      <input type="hidden" name="Test_Type" value="<?= $data["Test_Type"] ?>">

      <div class="card mb-3">
        <div class="card-header"><strong>Sample Info</strong></div>
        <div class="card-body">
          <p><strong>ID:</strong> <?= $data["Sample_ID"] ?></p>
          <p><strong>Number:</strong> <?= $data["Sample_Number"] ?></p>
          <p><strong>Test:</strong> <?= $data["Test_Type"] ?></p>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><strong>Checklist</strong></div>
        <div class="card-body">
          <?php
          $checks = [
            "ASTM compliance",
            "Data coherence",
            "Digit accuracy",
            "Sample integrity",
            "Graph consistency",
            "Procedure compliance"
          ];

          foreach ($checks as $c): ?>
            <div class="form-check mb-2">
              <input type="checkbox" class="form-check-input" name="checklist[]" value="<?= $c ?>">
              <label class="form-check-label"><?= $c ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><strong>Result</strong></div>
        <div class="card-body">
          <div class="form-check">
            <input type="radio" name="Status" value="Pass" class="form-check-input" checked>
            <label class="form-check-label">PASS</label>
          </div>
          <div class="form-check">
            <input type="radio" name="Status" value="Observado" class="form-check-input">
            <label class="form-check-label">OBSERVADO</label>
          </div>
          <div class="form-check">
            <input type="radio" name="Status" value="Fail" class="form-check-input">
            <label class="form-check-label">FAIL — Repeat</label>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><strong>Comments</strong></div>
        <div class="card-body">
          <textarea name="Comments" class="form-control" rows="4"></textarea>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><strong>Evidence</strong></div>
        <div class="card-body">
          <input type="file" class="form-control" name="Evidence">
        </div>
      </div>

      <button class="btn btn-primary">Save Review</button>
      <a href="../pages/essay-review.php" class="btn btn-secondary">Cancel</a>

    </form>
  </section>

</main>

<?php include_once('../components/footer.php'); ?>
