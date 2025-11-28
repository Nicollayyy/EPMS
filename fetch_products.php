<?php
header('Content-Type: application/json');
include 'database/db.php';

// Check if products table exists
$tableExists = false;
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows > 0) {
    $tableExists = true;
}

$categories = [];

if ($tableExists) {
    // Fetch all products grouped by category
    $stmt = $conn->query("SELECT product_id, name, category, size, price FROM products ORDER BY category, name");
    
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $category = $row['category'];
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }
            $categories[$category][] = [
                'product_id' => $row['product_id'],
                'name' => $row['name'],
                'size' => $row['size'],
                'price' => (float)$row['price']
            ];
        }
    }
}

echo json_encode([
    'categories' => $categories
]);

$conn->close();
?>
