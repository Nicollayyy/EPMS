<?php
include './db.php';

if(isset($_POST['source'], $_POST['amount'], $_POST['date'])){
    $source = $_POST['source'];
    $product_name = $_POST['product_name'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $notes = $_POST['notes'] ?? '';

    // Check if columns exist, if not add them
    $result = $conn->query("SHOW COLUMNS FROM profits LIKE 'product_name'");
    if($result->num_rows == 0) {
        $conn->query("ALTER TABLE profits ADD COLUMN product_name VARCHAR(255) AFTER source");
    }
    
    $result = $conn->query("SHOW COLUMNS FROM profits LIKE 'quantity'");
    if($result->num_rows == 0) {
        $conn->query("ALTER TABLE profits ADD COLUMN quantity INT DEFAULT 1 AFTER product_name");
    }

    $stmt = $conn->prepare("INSERT INTO profits (source, product_name, quantity, amount, date, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidss", $source, $product_name, $quantity, $amount, $date, $notes);
    $stmt->execute();

    // Redirect back to profit dashboard
    header("Location:./../html/profit.php?success=1");
    exit;
}
?>
