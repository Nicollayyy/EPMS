<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: html/login.html");
    exit();
}

include 'database/db.php';

// Get date from URL
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Fetch data
$profits = [];
$expenses = [];
$totalProfit = 0;
$totalExpense = 0;

if ($endDate) {
    // Date range
    $stmt = $conn->prepare("SELECT profit_id, source, amount, date, notes FROM profits WHERE DATE(date) BETWEEN ? AND ? ORDER BY date DESC");
    $stmt->bind_param("ss", $date, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $profits[] = $row;
        $totalProfit += $row['amount'];
    }
    
    $stmt = $conn->prepare("SELECT expense_id, category, amount, date FROM expenses WHERE DATE(date) BETWEEN ? AND ? ORDER BY date DESC");
    $stmt->bind_param("ss", $date, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
        $totalExpense += $row['amount'];
    }
    
    $period = date('M d, Y', strtotime($date)) . ' - ' . date('M d, Y', strtotime($endDate));
} else {
    // Single date
    $stmt = $conn->prepare("SELECT profit_id, source, amount, date, notes FROM profits WHERE DATE(date) = ? ORDER BY date DESC");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $profits[] = $row;
        $totalProfit += $row['amount'];
    }
    
    $stmt = $conn->prepare("SELECT expense_id, category, amount, date FROM expenses WHERE DATE(date) = ? ORDER BY date DESC");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
        $totalExpense += $row['amount'];
    }
    
    $period = date('F d, Y', strtotime($date));
}

// Create HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report - ' . $period . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #ffd700; padding-bottom: 20px; }
        .header h1 { color: #302014; margin: 0; font-size: 28px; }
        .header p { color: #666; margin: 5px 0; }
        .summary { background: #fff9e6; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 2px solid #ffd700; }
        .summary-row { display: flex; justify-content: space-between; margin: 10px 0; font-size: 16px; }
        .summary-row strong { font-size: 18px; }
        .profit { color: #28a745; }
        .expense { color: #dc3545; }
        .net { color: #302014; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #302014; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f9f9f9; }
        .section-title { color: #302014; margin-top: 30px; margin-bottom: 15px; font-size: 20px; border-bottom: 2px solid #ffd700; padding-bottom: 5px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-profit { background: #28a745; color: white; }
        .badge-expense { background: #dc3545; color: white; }
        .footer { text-align: center; margin-top: 50px; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>1028 Tea & Café</h1>
        <p>Financial Report</p>
        <p><strong>' . $period . '</strong></p>
    </div>
    
    <div class="summary">
        <div class="summary-row">
            <span>Total Profit:</span>
            <strong class="profit">₱ ' . number_format($totalProfit, 2) . '</strong>
        </div>
        <div class="summary-row">
            <span>Total Expense:</span>
            <strong class="expense">₱ ' . number_format($totalExpense, 2) . '</strong>
        </div>
        <div class="summary-row" style="border-top: 2px solid #ffd700; padding-top: 10px; margin-top: 10px;">
            <span>Net Income:</span>
            <strong class="net">₱ ' . number_format($totalProfit - $totalExpense, 2) . '</strong>
        </div>
    </div>
    
    <h2 class="section-title">Financial Records</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>';

$index = 1;
// Combine and sort records
$allRecords = [];
foreach ($profits as $profit) {
    $allRecords[] = [
        'type' => 'Profit',
        'desc' => $profit['source'] . ($profit['notes'] ? ' (' . $profit['notes'] . ')' : ''),
        'amount' => $profit['amount'],
        'date' => $profit['date'],
        'isProfit' => true
    ];
}
foreach ($expenses as $expense) {
    $allRecords[] = [
        'type' => 'Expense',
        'desc' => $expense['category'],
        'amount' => $expense['amount'],
        'date' => $expense['date'],
        'isProfit' => false
    ];
}

usort($allRecords, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

foreach ($allRecords as $record) {
    $badgeClass = $record['isProfit'] ? 'badge-profit' : 'badge-expense';
    $amountClass = $record['isProfit'] ? 'profit' : 'expense';
    
    $html .= '<tr>
        <td>' . $index++ . '</td>
        <td><span class="badge ' . $badgeClass . '">' . $record['type'] . '</span></td>
        <td>' . htmlspecialchars($record['desc']) . '</td>
        <td class="' . $amountClass . '"><strong>₱ ' . number_format($record['amount'], 2) . '</strong></td>
        <td>' . date('M d, Y', strtotime($record['date'])) . '</td>
    </tr>';
}

if (empty($allRecords)) {
    $html .= '<tr><td colspan="5" style="text-align: center; color: #999;">No records found for this period</td></tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p>Generated on ' . date('F d, Y h:i A') . '</p>
        <p>© 1028 Tea & Café — Expense & Profit Management</p>
    </div>
</body>
</html>';

// Using PDFShift API (50 PDFs/month free)
// Get your own key from: https://pdfshift.io/register
$api_key = 'sk_a0d66db0d1ec3eb416167746a14ca41f91a2bead';

$ch = curl_init('https://api.pdfshift.io/v3/convert/pdf');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $api_key,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'source' => $html,
    'landscape' => false,
    'use_print' => false
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode == 200 && $response) {
    // Success - send PDF to browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="financial_report_' . $date . '.pdf"');
    echo $response;
} else {
    // Error - show detailed message
    echo "<h2>Error Generating PDF</h2>";
    echo "<p>HTTP Status Code: " . $httpCode . "</p>";
    if ($curlError) {
        echo "<p>cURL Error: " . htmlspecialchars($curlError) . "</p>";
    }
    echo "<p>Response: " . htmlspecialchars(substr($response, 0, 500)) . "</p>";
    echo "<hr>";
    echo "<h3>HTML Preview:</h3>";
    echo $html;
    error_log("PDF API Error - HTTP Code: $httpCode, cURL Error: $curlError, Response: " . substr($response, 0, 200));
}

// Alternative free APIs if PDFShift doesn't work:
// 1. html2pdf.app - requires API key now (sign up at https://html2pdf.app)
// 2. api2pdf.com - 100 PDFs/month free (sign up at https://portal.api2pdf.com)
// 3. pdfcrowd.com - 100 PDFs/month free (sign up at https://pdfcrowd.com)
?>
