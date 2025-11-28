<?php
include 'database/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO expenses (category, amount, date) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $category, $amount, $date);

    if ($stmt->execute()) {
        header("Location: html/expense.php?success=1");
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Add Expense | 1028 Tea & Café</title>

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
        <div class="col-12 col-lg-8 mx-auto">

          <div class="panel p-4">
            <div class="panel-head mb-4">
              <h4 class="mb-1"><i class="fa-solid fa-plus me-2"></i>Add New Expense</h4>
              <small class="text-muted">Record a new expense entry</small>
            </div>

            <?php if(!empty($error)): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <form method="POST" action="">
              <div class="mb-4">
                <label for="category" class="form-label fw-semibold">Expense Category <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-select form-select-lg" required>
                  <option value="">-- Select Category --</option>
                  <option value="Supplies">Supplies</option>
                  <option value="Ingredients">Ingredients</option>
                  <option value="Staff Salary">Staff Salary</option>
                  <option value="Utilities">Utilities</option>
                  <option value="Rent">Rent</option>
                  <option value="Marketing">Marketing</option>
                  <option value="Others">Others</option>
                </select>
              </div>

              <div class="mb-4">
                <label for="amount" class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">₱</span>
                  <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" placeholder="0.00" required>
                </div>
              </div>

              <div class="mb-4">
                <label for="date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" id="date" class="form-control form-control-lg" required>
              </div>

              <div class="d-flex gap-2 justify-content-end">
                <a href="html/expense.php" class="btn btn-outline-secondary btn-lg"><i class="fa-solid fa-arrow-left me-2"></i>Cancel</a>
                <button type="submit" class="btn btn-gold btn-lg"><i class="fa-solid fa-check me-2"></i>Save Expense</button>
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
    // Set today's date by default
    const dateInput = document.getElementById('date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;

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
