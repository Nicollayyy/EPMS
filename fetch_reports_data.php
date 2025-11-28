<?php
header('Content-Type: application/json');
include 'database/db.php';

// Get the selected date(s) from query parameters
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Fetch profit records
$profits = [];
$totalProfit = 0;

if ($endDate) {
    // Date range query
    $stmt = $conn->prepare("SELECT profit_id, source, amount, date, notes 
                            FROM profits 
                            WHERE DATE(date) BETWEEN ? AND ? 
                            ORDER BY date DESC, profit_id DESC");
    $stmt->bind_param("ss", $selectedDate, $endDate);
} else {
    // Single date query
    $stmt = $conn->prepare("SELECT profit_id, source, amount, date, notes 
                            FROM profits 
                            WHERE DATE(date) = ? 
                            ORDER BY date DESC, profit_id DESC");
    $stmt->bind_param("s", $selectedDate);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $profits[] = [
        'profit_id' => $row['profit_id'],
        'source' => $row['source'],
        'amount' => (float)$row['amount'],
        'date' => $row['date'],
        'notes' => $row['notes']
    ];
    $totalProfit += (float)$row['amount'];
}

// Fetch expense records
$expenses = [];
$totalExpense = 0;

if ($endDate) {
    // Date range query
    $stmt = $conn->prepare("SELECT expense_id, category, amount, date 
                            FROM expenses 
                            WHERE DATE(date) BETWEEN ? AND ? 
                            ORDER BY date DESC, expense_id DESC");
    $stmt->bind_param("ss", $selectedDate, $endDate);
} else {
    // Single date query
    $stmt = $conn->prepare("SELECT expense_id, category, amount, date 
                            FROM expenses 
                            WHERE DATE(date) = ? 
                            ORDER BY date DESC, expense_id DESC");
    $stmt->bind_param("s", $selectedDate);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $expenses[] = [
        'expense_id' => $row['expense_id'],
        'category' => $row['category'],
        'amount' => (float)$row['amount'],
        'date' => $row['date']
    ];
    $totalExpense += (float)$row['amount'];
}

// Return the data
echo json_encode([
    'profits' => $profits,
    'expenses' => $expenses,
    'totalProfit' => round($totalProfit, 2),
    'totalExpense' => round($totalExpense, 2),
    'selectedDate' => $selectedDate,
    'endDate' => $endDate
]);

$conn->close();
?>
