<?php
include 'database/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: html/profit.php");
    exit;
}

$id = $_GET['id'];

// Fetch profit record to display details
$stmt = $conn->prepare("SELECT * FROM profits WHERE profit_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$profit = $result->fetch_assoc();

if (!$profit) {
    header("Location: html/profit.php?error=notfound");
    exit;
}

$error = '';

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $conn->prepare("DELETE FROM profits WHERE profit_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: html/profit.php?success=deleted");
        exit;
    } else {
        $error = "Error deleting record: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Delete Sale | 1028 Tea & Café</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="dash-body">

  <!-- Include Topbar -->
  <?php include 'Includes/topbar.php'; ?>

  <div class="app-shell d-flex">

    <!-- Include Sidebar -->
    <?php include 'Includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content p-4">
      <div class="row">
        <div class="col-12 col-lg-6 mx-auto">

          <div class="panel p-4">
            <div class="panel-head mb-4 text-center">
              <div class="mb-3">
                <i class="fa-solid fa-triangle-exclamation fa-3x text-danger"></i>
              </div>
              <h4 class="mb-1 text-danger">Delete Sale Record</h4>
              <small class="text-muted">This action cannot be undone</small>
            </div>

            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <div class="alert alert-warning mb-4">
              <h6 class="alert-heading"><i class="fa-solid fa-info-circle me-2"></i>Record Details</h6>
              <hr>
              <div class="row">
                <div class="col-6">
                  <strong>ID:</strong>
                </div>
                <div class="col-6">
                  #<?= $profit['profit_id'] ?>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <strong>Category:</strong>
                </div>
                <div class="col-6">
                  <?= htmlspecialchars($profit['source']) ?>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <strong>Amount:</strong>
                </div>
                <div class="col-6">
                  ₱ <?= number_format($profit['amount'], 2) ?>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-6">
                  <strong>Date:</strong>
                </div>
                <div class="col-6">
                  <?= date('M d, Y', strtotime($profit['date'])) ?>
                </div>
              </div>
              <?php if (!empty($profit['notes'])): ?>
              <div class="row mt-2">
                <div class="col-6">
                  <strong>Notes:</strong>
                </div>
                <div class="col-6">
                  <?= htmlspecialchars($profit['notes']) ?>
                </div>
              </div>
              <?php endif; ?>
            </div>

            <div class="alert alert-danger">
              <i class="fa-solid fa-exclamation-triangle me-2"></i>
              <strong>Warning:</strong> Are you sure you want to delete this sale record? This action is permanent and cannot be reversed.
            </div>

            <form method="POST" action="">
              <div class="d-flex gap-2 justify-content-center">
                <a href="html/profit.php" class="btn btn-outline-secondary btn-lg">
                  <i class="fa-solid fa-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                  <i class="fa-solid fa-trash me-2"></i>Delete Permanently
                </button>
              </div>
            </form>

          </div>

        </div>
      </div>

      <footer class="mt-4 text-center text-muted small">
        © 1028 Tea & Café — Expense & Profit Management
      </footer>
    </main>

  </div>

  <!-- Bootstrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Sidebar toggle
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('sidebar');
    btnToggle?.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

    // Profile dropdown
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');
    profileToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle('show');
    });
    document.addEventListener('click', () => profileMenu.classList.remove('show'));
  </script>
</body>
</html>
