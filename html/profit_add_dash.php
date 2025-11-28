<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>1028 Tea & Café — Add Profit</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dash-body">

  <!-- Include Topbar -->
  <?php include './../Includes/topbar.php'; ?>

  <div class="app-shell d-flex">

    <!-- Include Sidebar -->
    <?php include './../Includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content p-4">
      <div class="row">
        <div class="col-12 col-lg-8 mx-auto">

          <div class="panel p-4">
            <div class="panel-head mb-4">
              <h4 class="mb-1">Add New Sale</h4>
              <small class="text-muted">Record a new profit entry from sales</small>
            </div>

            <form id="profitForm" action="./../database/profit_save.php" method="POST">
              <div class="mb-4">
                <label for="source" class="form-label fw-semibold">Product Category <span class="text-danger">*</span></label>
                <select name="source" id="source" class="form-select form-select-lg" required>
                  <option value="">-- Select Product Category --</option>
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
                <label for="product_name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="product_name" id="product_name" class="form-control form-control-lg" placeholder="e.g., Wintermelon Milk Tea, Blueberry Cheesecake" required>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label for="quantity" class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                  <input type="number" min="1" name="quantity" id="quantity" class="form-control form-control-lg" placeholder="1" value="1" required>
                </div>

                <div class="col-md-6 mb-4">
                  <label for="amount" class="form-label fw-semibold">Sale Amount <span class="text-danger">*</span></label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text">₱</span>
                    <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" placeholder="0.00" required>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label for="date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" id="date" class="form-control form-control-lg" required>
              </div>

              <div class="mb-4">
                <label for="notes" class="form-label fw-semibold">Notes <span class="text-muted">(Optional)</span></label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any additional details about this sale..."></textarea>
              </div>

              <div class="d-flex gap-2 justify-content-end">
                <a href="./profit.php" class="btn btn-outline-secondary btn-lg"><i class="fa-solid fa-arrow-left me-2"></i>Cancel</a>
                <button type="submit" class="btn btn-gold btn-lg"><i class="fa-solid fa-check me-2"></i>Save Sale</button>
              </div>
            </form>

          </div>

        </div>
      </div>
    </main>

  </div>

  <!-- Bootstrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Set today's date by default
    const dateInput = document.getElementById('date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;
  </script>
</body>
</html>
