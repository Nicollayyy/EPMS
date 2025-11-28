<?php
header('Content-Type: application/json');
include 'database/db.php';

// Today's Profit
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$todayProfit = (float)$stmt->get_result()->fetch_assoc()['total'];

// This Month Profit
$currentMonth = date('Y-m-01');
$nextMonth = date('Y-m-01', strtotime('+1 month'));
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date >= ? AND date < ?");
$stmt->bind_param("ss", $currentMonth, $nextMonth);
$stmt->execute();
$monthProfit = (float)$stmt->get_result()->fetch_assoc()['total'];

// Product Count (if you have a products table)
$productCount = 0;
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) AS count FROM products");
    if ($result) {
        $productCount = (int)$result->fetch_assoc()['count'];
    }
}

// Recent Profits (last 10 records)
$recentProfits = [];
$stmt = $conn->prepare("SELECT profit_id, source, product_name, quantity, amount, date, notes FROM profits ORDER BY date DESC, profit_id DESC LIMIT 10");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recentProfits[] = [
        'profit_id' => $row['profit_id'],
        'source' => $row['source'],
        'product_name' => $row['product_name'] ?? '',
        'quantity' => (int)($row['quantity'] ?? 1),
        'amount' => (float)$row['amount'],
        'date' => $row['date'],
        'notes' => $row['notes']
    ];
}

// Category Breakdown (current month)
$categoryBreakdown = [];
$stmt = $conn->prepare("SELECT source AS category, SUM(amount) AS total 
                       FROM profits 
                       WHERE date >= ? AND date < ? 
                       GROUP BY source 
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

// Daily Sales (based on period parameter)
$period = isset($_GET['period']) ? $_GET['period'] : '7';
$dailySales = [];

if ($period === 'today') {
    // Hourly breakdown for today
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $total = (float)$stmt->get_result()->fetch_assoc()['total'];
    
    $dailySales[] = [
        'date' => 'Today',
        'total' => round($total, 2)
    ];
} else {
    // Daily breakdown for specified number of days
    $days = (int)$period;
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM profits WHERE date = ?");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $total = (float)$stmt->get_result()->fetch_assoc()['total'];
        
        $dailySales[] = [
            'date' => date('M d', strtotime($date)),
            'total' => round($total, 2)
        ];
    }
}

echo json_encode([
    'todayProfit' => round($todayProfit, 2),
    'monthProfit' => round($monthProfit, 2),
    'productCount' => $productCount,
    'recentProfits' => $recentProfits,
    'categoryBreakdown' => $categoryBreakdown,
    'dailySales' => $dailySales
]);

$conn->close();
?>
