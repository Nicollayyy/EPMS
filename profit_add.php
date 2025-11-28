<?php
include 'database/db.php'; // your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO profits (source, amount, date, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $source, $amount, $date, $notes);

    if ($stmt->execute()) {
        header("Location:./profit_list.php?status=success");
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
  <title>1028 Tea & Café — Add Profit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dash-body">

  <?php include 'Includes/topbar.php'; ?>
  <?php include 'Includes/sidebar.php'; ?>

  <main class="content p-4">
    <h2 class="mb-3">Add Profit</h2>

    <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <div class="panel p-4">
      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Category / Source</label>
          <select name="source" class="form-select" required>
            <option value="">--Select Category--</option>
            <option value="Milktea">Milktea</option>
            <option value="Cheesecake">Cheesecake</option>
            <option value="Regular Frappe">Regular Frappe</option>
            <option value="Fruit Tea">Fruit Tea</option>
            <option value="Iced Coffee">Iced Coffee</option>
            <option value="Fruit Soda">Fruit Soda</option>
            <option value="Hot drinks">Hot drinks</option>
            <option value="Premium Frappe">Premium Frappe</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Amount</label>
          <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Date</label>
          <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-gold"><i class="fa-solid fa-plus me-2"></i>Save Profit</button>
        <a href="profit_list.php" class="btn btn-outline-light">Cancel</a>
      </form>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
