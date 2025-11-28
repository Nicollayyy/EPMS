<?php
include 'database/db.php'; // database connection

$result = $conn->query("SELECT * FROM profits ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profit List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Profit List</h2>
<a href="./profit_add.php" class="btn btn-success mb-3">Add New Profit</a>
<table class="table table-bordered">
    <thead>
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
            <td><?= $row['profit_id'] ?></td>
            <td><?= htmlspecialchars($row['source']) ?></td>
            <td>₱ <?= number_format($row['amount'],2) ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= htmlspecialchars($row['notes']) ?></td>
            <td>
                <a href="profit_edit.php?id=<?= $row['profit_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="profit_delete.php?id=<?= $row['profit_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
