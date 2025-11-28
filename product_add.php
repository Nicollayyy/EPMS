<?php
include 'database/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO products (name, category, size, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $category, $size, $price);

    if ($stmt->execute()) {
        header("Location: html/products.php?success=1");
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
  <title>Add Product | 1028 Tea & Café</title>

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
              <h4 class="mb-1"><i class="fa-solid fa-plus me-2"></i>Add New Product</h4>
              <small class="text-muted">Add a new item to your menu</small>
            </div>

            <?php if(!empty($error)): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <form method="POST" action="">
              <div class="mb-4">
                <label for="name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control form-control-lg" placeholder="e.g., Classic Milk Tea" required>
              </div>

              <div class="mb-4">
                <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-select form-select-lg" required>
                  <option value="">-- Select Category --</option>
                  <option value="Milk Tea">Milk Tea</option>
                  <option value="Cheesecake">Cheesecake</option>
                  <option value="Regular Frappe">Regular Frappe</option>
                  <option value="Fruit Tea">Fruit Tea</option>
                  <option value="Iced Coffee">Iced Coffee</option>
                  <option value="Fruit Soda">Fruit Soda</option>
                  <option value="Hot Drinks">Hot Drinks</option>
                  <option value="Premium Frappe">Premium Frappe</option>
                </select>
              </div>

              <div class="mb-4">
                <label for="size" class="form-label fw-semibold">Size <span class="text-danger">*</span></label>
                <select name="size" id="size" class="form-select form-select-lg" required>
                  <option value="">-- Select Size --</option>
                  <option value="Medium">Medium</option>
                  <option value="Large">Large</option>
                  <option value="One Size">One Size</option>
                </select>
              </div>

              <div class="mb-4">
                <label for="price" class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">₱</span>
                  <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" placeholder="0.00" required>
                </div>
              </div>

              <div class="d-flex gap-2 justify-content-end">
                <a href="html/products.php" class="btn btn-outline-secondary btn-lg"><i class="fa-solid fa-arrow-left me-2"></i>Cancel</a>
                <button type="submit" class="btn btn-gold btn-lg"><i class="fa-solid fa-check me-2"></i>Add Product</button>
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
