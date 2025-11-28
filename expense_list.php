<?php
include 'database/db.php';
$result = $conn->query("SELECT * FROM expenses ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>1028 Tea & Café — Expense List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dash-body">

<?php include './../Includes/topbar.php'; ?>
<?php include './../Includes/sidebar.php'; ?>

<main class="content p-4">
<div class="d-flex justify-content-between align-items-center mb-3">
<h2>Expense List</h2>
<a href="expense_add.php" class="btn btn-gold"><i class="fa-solid fa-plus me-2"></i>Add Expense</a>
</div>

<div class="panel p-3">
<table class="table table-hover table-bordered mb-0">
<thead class="table-light">
<tr>
<th>ID</th>
<th>Category</th>
<th>Amount</th>
<th>Date</th>
<th>Notes</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['expense_id'] ?></td>
<td><?= htmlspecialchars($row['category']) ?></td>
<td>₱ <?= number_format($row['amount'],2) ?></td>
<td><?= $row['date'] ?></td>
<td><?= htmlspecialchars($row['notes']) ?></td>
<td>
<a href="expense_edit.php?id=<?= $row['expense_id'] ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
<a href="expense_delete.php?id=<?= $row['expense_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')"><i class="fa-solid fa-trash"></i></a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
