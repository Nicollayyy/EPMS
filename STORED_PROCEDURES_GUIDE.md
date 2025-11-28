
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

## Testing Your Procedures

```sql
-- Test adding a profit
CALL sp_add_profit('Test Sale', 100.00, CURDATE(), 'Test note');

-- Test getting profits
CALL sp_get_profits_by_date(CURDATE());

-- Test monthly summary
CALL sp_get_monthly_summary(YEAR(CURDATE()), MONTH(CURDATE()));
```
