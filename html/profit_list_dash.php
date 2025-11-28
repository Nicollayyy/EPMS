<?php
include './../database/db.php'; // adjust the path to your db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profit Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dash-body">

<?php include './../Includes/topbar.php'; ?>
<div class="app-shell d-flex">
<?php include './../Includes/sidebar.php'; ?>

<main class="content p-4">
  <div class="row">
    <div class="col-12">
      <div class="panel p-4 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h4 class="mb-1">Sales Records</h4>
            <p class="text-muted small mb-0">View and manage all profit entries</p>
          </div>
          <a href="profit_add_dash.php" class="btn btn-gold"><i class="fa-solid fa-plus me-2"></i>Add Sale</a>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Notes</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT * FROM profits ORDER BY date DESC, profit_id DESC");
            if($result && $result->num_rows > 0){
                $counter = 1;
                while($row = $result->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>{$counter}</td>";
                    echo "<td><span class='badge bg-secondary'>".htmlspecialchars($row['source'])."</span></td>";
                    echo "<td class='fw-bold text-success'>₱ ".number_format($row['amount'],2)."</td>";
                    echo "<td>".date('M d, Y', strtotime($row['date']))."</td>";
                    echo "<td>".htmlspecialchars($row['notes'] ?: '-')."</td>";
                    echo "<td>
                            <a href='../profit_edit.php?id={$row['profit_id']}' class='btn btn-sm btn-warning'><i class='fa-solid fa-edit'></i></a>
                            <a href='../profit_delete.php?id={$row['profit_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this record?\")'><i class='fa-solid fa-trash'></i></a>
                          </td>";
                    echo "</tr>";
                    $counter++;
                }
            } else {
                echo "<tr><td colspan='6' class='text-center text-muted'>No sales records found</td></tr>";
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
