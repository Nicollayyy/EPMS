-- Stored Procedures for 1028 Café Management System
-- Run this SQL file in your phpMyAdmin or MySQL client

DELIMITER $$

-- 1. Procedure to add a new profit record
CREATE PROCEDURE IF NOT EXISTS sp_add_profit(
    IN p_source VARCHAR(255),
    IN p_amount DECIMAL(10,2),
    IN p_date DATE,
    IN p_notes TEXT
)
BEGIN
    INSERT INTO profits (source, amount, date, notes)
    VALUES (p_source, p_amount, p_date, p_notes);
    
    SELECT LAST_INSERT_ID() as profit_id, 'Success' as status;
END$$

-- 2. Procedure to add a new expense record
CREATE PROCEDURE IF NOT EXISTS sp_add_expense(
    IN p_category VARCHAR(100),
    IN p_amount DECIMAL(10,2),
    IN p_date DATE,
    IN p_notes TEXT
)
BEGIN
    INSERT INTO expenses (category, amount, date, notes)
    VALUES (p_category, p_amount, p_date, p_notes);
    
    SELECT LAST_INSERT_ID() as expense_id, 'Success' as status;
END$$

-- 3. Procedure to get profit records by date
CREATE PROCEDURE IF NOT EXISTS sp_get_profits_by_date(
    IN p_date DATE
)
BEGIN
    SELECT profit_id, source, amount, date, notes
    FROM profits
    WHERE date = p_date
    ORDER BY date DESC;
END$$

-- 4. Procedure to get expense records by date
CREATE PROCEDURE IF NOT EXISTS sp_get_expenses_by_date(
    IN p_date DATE
)
BEGIN
    SELECT expense_id, category, amount, date, notes
    FROM expenses
    WHERE date = p_date
    ORDER BY date DESC;
END$$

-- 5. Procedure to get monthly summary
CREATE PROCEDURE IF NOT EXISTS sp_get_monthly_summary(
    IN p_year INT,
    IN p_month INT
)
BEGIN
    SELECT 
        COALESCE(SUM(p.amount), 0) as total_profit,
        COALESCE(SUM(e.amount), 0) as total_expense,
        COALESCE(SUM(p.amount), 0) - COALESCE(SUM(e.amount), 0) as net_income
    FROM 
        (SELECT amount FROM profits WHERE YEAR(date) = p_year AND MONTH(date) = p_month) p,
        (SELECT amount FROM expenses WHERE YEAR(date) = p_year AND MONTH(date) = p_month) e;
END$$

-- 6. Procedure to authenticate user
CREATE PROCEDURE IF NOT EXISTS sp_authenticate_user(
    IN p_username VARCHAR(100)
)
BEGIN
    SELECT admin_id, username, password, phone_number
    FROM admin
    WHERE username = p_username
    LIMIT 1;
END$$

-- 7. Procedure to update password
CREATE PROCEDURE IF NOT EXISTS sp_update_password(
    IN p_phone VARCHAR(20),
    IN p_new_password VARCHAR(255)
)
BEGIN
    UPDATE admin
    SET password = p_new_password
    WHERE phone_number = p_phone;
    
    SELECT ROW_COUNT() as affected_rows, 'Success' as status;
END$$

-- 8. Procedure to delete profit record
CREATE PROCEDURE IF NOT EXISTS sp_delete_profit(
    IN p_profit_id INT
)
BEGIN
    DELETE FROM profits WHERE profit_id = p_profit_id;
    SELECT ROW_COUNT() as affected_rows, 'Deleted' as status;
END$$

-- 9. Procedure to delete expense record
CREATE PROCEDURE IF NOT EXISTS sp_delete_expense(
    IN p_expense_id INT
)
BEGIN
    DELETE FROM expenses WHERE expense_id = p_expense_id;
    SELECT ROW_COUNT() as affected_rows, 'Deleted' as status;
END$$

-- 10. Procedure to get all products
CREATE PROCEDURE IF NOT EXISTS sp_get_all_products()
BEGIN
    SELECT product_id, name, category, size, price
    FROM products
    ORDER BY category, name;
END$$

-- 11. Procedure to add product
CREATE PROCEDURE IF NOT EXISTS sp_add_product(
    IN p_name VARCHAR(255),
    IN p_category VARCHAR(100),
    IN p_size VARCHAR(50),
    IN p_price DECIMAL(10,2)
)
BEGIN
    INSERT INTO products (name, category, size, price)
    VALUES (p_name, p_category, p_size, p_price);
    
    SELECT LAST_INSERT_ID() as product_id, 'Success' as status;
END$$

-- 12. Procedure to get dashboard statistics
CREATE PROCEDURE IF NOT EXISTS sp_get_dashboard_stats()
BEGIN
    -- Today's sales
    SELECT 
        COALESCE(SUM(amount), 0) as today_sales
    FROM profits
    WHERE date = CURDATE();
    
    -- This month profit
    SELECT 
        COALESCE(SUM(amount), 0) as month_profit
    FROM profits
    WHERE YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE());
    
    -- This month expense
    SELECT 
        COALESCE(SUM(amount), 0) as month_expense
    FROM expenses
    WHERE YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE());
    
    -- Product count
    SELECT COUNT(*) as product_count FROM products;
END$$

DELIMITER ;

-- Show all created procedures
SHOW PROCEDURE STATUS WHERE Db = DATABASE();
