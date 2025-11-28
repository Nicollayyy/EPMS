<?php
session_start();
header('Content-Type: application/json');
include 'database/db.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$stmt = $conn->prepare("SELECT file_id, file_name, file_path, drive_link, upload_date FROM files ORDER BY upload_date DESC");
$stmt->execute();
$result = $stmt->get_result();

$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}

echo json_encode(['success' => true, 'files' => $files]);

$stmt->close();
$conn->close();
?>
