<?php
header('Content-Type: application/json');
include 'database/db.php';

// Today's Expenses
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM expenses WHERE date = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$todayExpense = (float)$stmt->get_result()->fetch_assoc()['total'];

// This Month Expenses
$currentMonth = date('Y-m-01');
$nextMonth = date('Y-m-01', strtotime('+1 month'));
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM expenses WHERE date >= ? AND date < ?");
$stmt->bind_param("ss", $currentMonth, $nextMonth);
$stmt->execute();
$monthExpense = (float)$stmt->get_result()->fetch_assoc()['total'];

// Category Count
$stmt = $conn->prepare("SELECT COUNT(DISTINCT category) AS count FROM expenses WHERE date >= ? AND date < ?");
$stmt->bind_param("ss", $currentMonth, $nextMonth);
$stmt->execute();
$categoryCount = (int)$stmt->get_result()->fetch_assoc()['count'];

// Recent Expenses (last 10 records)
$recentExpenses = [];
$stmt = $conn->prepare("SELECT expense_id, category, amount, date FROM expenses ORDER BY date DESC, expense_id DESC LIMIT 10");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recentExpenses[] = [
        'expense_id' => $row['expense_id'],
        'category' => $row['category'],
        'amount' => (float)$row['amount'],
        'date' => $row['date']
    ];
}

// Category Breakdown (current month)
$categoryBreakdown = [];
$stmt = $conn->prepare("SELECT category, SUM(amount) AS total 
                       FROM expenses 
                       WHERE date >= ? AND date < ? 
                       GROUP BY category 
                       ORDER BY total DESC");
$stmt->bind_param("ss", $currentMonth, $nextMonth);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (!empty($row['category'])) {
        $categoryBreakdown[] = [
            'category' => $row['category'],
            'total' => (float)$row['total']
        ];
    }
}

// Daily Expenses (last 7 days)
$dailyExpenses = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM expenses WHERE date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $total = (float)$stmt->get_result()->fetch_assoc()['total'];
    
    $dailyExpenses[] = [
        'date' => date('M d', strtotime($date)),
        'total' => round($total, 2)
    ];
}

echo json_encode([
    'todayExpense' => round($todayExpense, 2),
    'monthExpense' => round($monthExpense, 2),
    'categoryCount' => $categoryCount,
    'recentExpenses' => $recentExpenses,
    'categoryBreakdown' => $categoryBreakdown,
    'dailyExpenses' => $dailyExpenses
]);

$conn->close();
?>
