<?php

function get_month_total($conn, $table) {
    $currentMonth = date('Y-m-01');
    $nextMonth = date('Y-m-01', strtotime('+1 month'));

    // This Month
    $stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM $table WHERE date >= ? AND date < ?");
    $stmt->bind_param("ss", $currentMonth, $nextMonth);
    $stmt->execute();
    $current = (float)$stmt->get_result()->fetch_assoc()['total'];

    // Previous Month
    $prevStart = date('Y-m-01', strtotime('-1 month'));
    $prevEnd = $currentMonth;

    $stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM $table WHERE date >= ? AND date < ?");
    $stmt->bind_param("ss", $prevStart, $prevEnd);
    $stmt->execute();
    $previous = (float)$stmt->get_result()->fetch_assoc()['total'];

    // % Difference
    if ($previous == 0) {
        $change = $current > 0 ? 100 : 0;
    } else {
        $change = (($current - $previous) / $previous) * 100;
    }

    // Direction
    if ($change > 0) { $dir = "up"; $symbol = "▲"; }
    elseif ($change < 0) { $dir = "down"; $symbol = "▼"; }
    else { $dir = "flat"; $symbol = "—"; }

    return [
        "this" => $current,
        "prev" => $previous,
        "change" => round($change, 2),
        "dir" => $dir,
        "symbol" => $symbol
    ];
}

$profit_stats = get_month_total($conn, "profits");
$expense_stats = get_month_total($conn, "expenses");
