<?php
header('Content-Type: application/json');
include 'database/db.php';

// Get selected years from query parameters
$profitYear = isset($_GET['profitYear']) ? intval($_GET['profitYear']) : date('Y');
$expenseYear = isset($_GET['expenseYear']) ? intval($_GET['expenseYear']) : date('Y');

// Build 12 months labels for each year
$profitLabels = [];
$expenseLabels = [];

for ($i = 1; $i <= 12; $i++) {
    $profitLabels[] = sprintf('%04d-%02d', $profitYear, $i);
    $expenseLabels[] = sprintf('%04d-%02d', $expenseYear, $i);
}

// Initialize arrays
$profits = array_fill(0, 12, 0.00);
$expenses = array_fill(0, 12, 0.00);

// Helper to fetch data for a specific year
function fill_year_data($conn, $table, $year, &$arr, $date_col = 'date') {
    $start = sprintf('%04d-01-01', $year);
    $end = sprintf('%04d-12-31', $year);
    
    $sql = "SELECT DATE_FORMAT({$date_col}, '%Y-%m') AS ym, IFNULL(SUM(amount),0) AS total
            FROM {$table}
            WHERE {$date_col} >= ? AND {$date_col} <= ?
            GROUP BY ym
            ORDER BY ym ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    $map = [];
    
    while ($r = $result->fetch_assoc()) {
        $map[$r['ym']] = (float)$r['total'];
    }

    // Map into array (12 months)
    for ($i = 1; $i <= 12; $i++) {
        $key = sprintf('%04d-%02d', $year, $i);
        $arr[$i - 1] = isset($map[$key]) ? round($map[$key], 2) : 0.00;
    }
}

fill_year_data($conn, 'profits', $profitYear, $profits, 'date');
fill_year_data($conn, 'expenses', $expenseYear, $expenses, 'date');

// Create month labels (Jan, Feb, etc.)
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

echo json_encode([
    'labels' => $monthLabels,
    'profits' => $profits,
    'expenses' => $expenses,
    'profitYear' => $profitYear,
    'expenseYear' => $expenseYear
]);
