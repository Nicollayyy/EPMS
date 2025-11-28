<?php
header('Content-Type: application/json');
include 'database/db.php';

// Today's Sales (Profit)
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$todaySales = (float)$stmt->get_result()->fetch_assoc()['total'];

// Yesterday's Sales (for comparison)
$yesterday = date('Y-m-d', strtotime('-1 day'));
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date = ?");
$stmt->bind_param("s", $yesterday);
$stmt->execute();
$yesterdaySales = (float)$stmt->get_result()->fetch_assoc()['total'];

// Calculate today's sales percentage change
$todaySalesChange = 0;
if ($yesterdaySales > 0) {
    $todaySalesChange = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100;
}

// This Month Profit
$currentMonth = date('Y-m-01');
$nextMonth = date('Y-m-01', strtotime('+1 month'));
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date >= ? AND date < ?");
$stmt->bind_param("ss", $currentMonth, $nextMonth);
$stmt->execute();
$monthProfit = (float)$stmt->get_result()->fetch_assoc()['total'];

// Last Month Profit (for comparison)
$lastMonth = date('Y-m-01', strtotime('-1 month'));
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date >= ? AND date < ?");
$stmt->bind_param("ss", $lastMonth, $currentMonth);
$stmt->execute();
$lastMonthProfit = (float)$stmt->get_result()->fetch_assoc()['total'];

// Calculate month profit percentage change
$monthProfitChange = 0;
if ($lastMonthProfit > 0) {
    $monthProfitChange = (($monthProfit - $lastMonthProfit) / $lastMonthProfit) * 100;
}

// This Month Expense
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM expenses WHERE date >= ? AND date < ?");
$stmt->bind_param("ss", $currentMonth, $nextMonth);
$stmt->execute();
$monthExpense = (float)$stmt->get_result()->fetch_assoc()['total'];

// Last Month Expense (for comparison)
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM expenses WHERE date >= ? AND date < ?");
$stmt->bind_param("ss", $lastMonth, $currentMonth);
$stmt->execute();
$lastMonthExpense = (float)$stmt->get_result()->fetch_assoc()['total'];

// Calculate month expense percentage change
$monthExpenseChange = 0;
if ($lastMonthExpense > 0) {
    $monthExpenseChange = (($monthExpense - $lastMonthExpense) / $lastMonthExpense) * 100;
}

// Product Count (if you have a products table)
$productCount = 0;
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) AS count FROM products");
    if ($result) {
        $productCount = (int)$result->fetch_assoc()['count'];
    }
}

// Category breakdown (if you have category field in profits table)
$categories = [];
$result = $conn->query("SHOW COLUMNS FROM profits LIKE 'category'");
if ($result->num_rows > 0) {
    // Category field exists
    $stmt = $conn->prepare("SELECT category AS name, SUM(amount) AS total 
                           FROM profits 
                           WHERE date >= ? AND date < ? 
                           GROUP BY category 
                           ORDER BY total DESC 
                           LIMIT 6");
    $stmt->bind_param("ss", $currentMonth, $nextMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['name'])) {
            $categories[] = [
                'name' => $row['name'],
                'total' => (float)$row['total']
            ];
        }
    }
}

// If no categories, try expense categories
if (empty($categories)) {
    $result = $conn->query("SHOW COLUMNS FROM expenses LIKE 'category'");
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("SELECT category AS name, SUM(amount) AS total 
                               FROM expenses 
                               WHERE date >= ? AND date < ? 
                               GROUP BY category 
                               ORDER BY total DESC 
                               LIMIT 6");
        $stmt->bind_param("ss", $currentMonth, $nextMonth);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (!empty($row['name'])) {
                $categories[] = [
                    'name' => $row['name'],
                    'total' => (float)$row['total']
                ];
            }
        }
    }
}

echo json_encode([
    'todaySales' => round($todaySales, 2),
    'todaySalesChange' => round($todaySalesChange, 1),
    'monthProfit' => round($monthProfit, 2),
    'monthProfitChange' => round($monthProfitChange, 1),
    'monthExpense' => round($monthExpense, 2),
    'monthExpenseChange' => round($monthExpenseChange, 1),
    'productCount' => $productCount,
    'categories' => $categories
]);

$conn->close();
?>
