# Stored Procedures Guide - 1028 Café Management System

## Installation

1. Open phpMyAdmin
2. Select your database: `expense_profit_monitoring`
3. Click on "SQL" tab
4. Copy and paste the contents of `database/create_stored_procedures.sql`
5. Click "Go" to execute

## Available Stored Procedures

### 1. Add Profit
```sql
CALL sp_add_profit('Sales', 5000.00, '2025-11-27', 'Daily sales');
```

### 2. Add Expense
```sql
CALL sp_add_expense('Supplies', 1500.00, '2025-11-27', 'Coffee beans');
```

### 3. Get Profits by Date
```sql
CALL sp_get_profits_by_date('2025-11-27');
```

### 4. Get Expenses by Date
```sql
CALL sp_get_expenses_by_date('2025-11-27');
```

### 5. Get Monthly Summary
```sql
CALL sp_get_monthly_summary(2025, 11);
```

### 6. Authenticate User
```sql
CALL sp_authenticate_user('1028_cafe');
```

### 7. Update Password
```sql
CALL sp_update_password('09999595754', '$2y$10$hashedpassword...');
```

### 8. Delete Profit
```sql
CALL sp_delete_profit(1);
```

### 9. Delete Expense
```sql
CALL sp_delete_expense(1);
```

### 10. Get All Products
```sql
CALL sp_get_all_products();
```

### 11. Add Product
```sql
CALL sp_add_product('Caramel Macchiato', 'Coffee', 'Medium', 150.00);
```

### 12. Get Dashboard Statistics
```sql
CALL sp_get_dashboard_stats();
```

## How to Call from PHP

Example of calling a stored procedure from PHP:

```php
// Call sp_add_profit
$stmt = $conn->prepare("CALL sp_add_profit(?, ?, ?, ?)");
$stmt->bind_param("sdss", $source, $amount, $date, $notes);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
echo "Profit ID: " . $row['profit_id'];
$stmt->close();

// Call sp_get_profits_by_date
$stmt = $conn->prepare("CALL sp_get_profits_by_date(?)");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    echo $row['source'] . ": " . $row['amount'];
}
$stmt->close();
```

## Benefits for Your Project

1. **Demonstrates SQL Knowledge**: Shows your professor you understand advanced database concepts
2. **Encapsulation**: Business logic is stored in the database
3. **Reusability**: Same procedures can be called from different parts of your application
4. **Performance**: Procedures are pre-compiled and optimized
5. **Security**: Additional layer of security by limiting direct table access

## Testing Your Procedures

Run this in phpMyAdmin SQL tab to test:

```sql
-- Test adding a profit
CALL sp_add_profit('Test Sale', 100.00, CURDATE(), 'Test note');

-- Test getting profits
CALL sp_get_profits_by_date(CURDATE());

-- Test monthly summary
CALL sp_get_monthly_summary(YEAR(CURDATE()), MONTH(CURDATE()));
```

## Note

Your current application will continue to work as-is. These stored procedures are additional functionality that you can demonstrate to your professor. You can optionally update your PHP code to use these procedures instead of direct queries.
