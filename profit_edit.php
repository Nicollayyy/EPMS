<?php
include 'database/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: html/profit.php");
    exit;
}

$id = $_GET['id'];

// Fetch profit record
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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'];
    $product_name = $_POST['product_name'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("UPDATE profits SET source=?, product_name=?, quantity=?, amount=?, date=?, notes=? WHERE profit_id=?");
    $stmt->bind_param("ssidssi", $source, $product_name, $quantity, $amount, $date, $notes, $id);

    if ($stmt->execute()) {
        $success = "Sale record updated successfully!";
        // Refresh the profit data
        $stmt = $conn->prepare("SELECT * FROM profits WHERE profit_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profit = $result->fetch_assoc();
    } else {
        $error = "Error updating record: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Edit Sale | 1028 Tea & Café</title>

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
              <h4 class="mb-1"><i class="fa-solid fa-edit me-2"></i>Edit Sale Record</h4>
              <small class="text-muted">Update the details for sale #<?= $profit['profit_id'] ?></small>
            </div>

            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if ($success): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <form method="POST" action="">
              <div class="mb-4">
                <label for="source" class="form-label fw-semibold">Product Category <span class="text-danger">*</span></label>
                <select name="source" id="source" class="form-select form-select-lg" required>
                  <?php
                  $categories = ['Milk Tea','Cheesecake','Regular Frappe','Fruit Tea','Iced Coffee','Fruit Soda','Hot Drinks','Premium Frappe'];
                  foreach ($categories as $cat) {
                      $selected = ($profit['source'] === $cat) ? 'selected' : '';
                      echo "<option value='$cat' $selected>$cat</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-4">
                <label for="product_name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="product_name" id="product_name" class="form-control form-control-lg" value="<?= htmlspecialchars($profit['product_name'] ?? '') ?>" placeholder="e.g., Wintermelon Milk Tea" required>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label for="quantity" class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                  <input type="number" min="1" name="quantity" id="quantity" class="form-control form-control-lg" value="<?= $profit['quantity'] ?? 1 ?>" required>
                </div>

                <div class="col-md-6 mb-4">
                  <label for="amount" class="form-label fw-semibold">Sale Amount <span class="text-danger">*</span></label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text">₱</span>
                    <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" value="<?= $profit['amount'] ?>" required>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label for="date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" id="date" class="form-control form-control-lg" value="<?= $profit['date'] ?>" required>
              </div>

              <div class="mb-4">
                <label for="notes" class="form-label fw-semibold">Notes <span class="text-muted">(Optional)</span></label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any additional details..."><?= htmlspecialchars($profit['notes']) ?></textarea>
              </div>

              <div class="d-flex gap-2 justify-content-end">
                <a href="html/profit.php" class="btn btn-outline-secondary btn-lg"><i class="fa-solid fa-arrow-left me-2"></i>Cancel</a>
                <button type="submit" class="btn btn-gold btn-lg"><i class="fa-solid fa-save me-2"></i>Update Sale</button>
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
