-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 02:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expense_profit_monitoring`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_expense` (IN `p_category` VARCHAR(100), IN `p_amount` DECIMAL(10,2), IN `p_date` DATE, IN `p_notes` TEXT)   BEGIN
    INSERT INTO expenses (category, amount, date, notes)
    VALUES (p_category, p_amount, p_date, p_notes);
    
    SELECT LAST_INSERT_ID() as expense_id, 'Success' as status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_product` (IN `p_name` VARCHAR(255), IN `p_category` VARCHAR(100), IN `p_size` VARCHAR(50), IN `p_price` DECIMAL(10,2))   BEGIN
    INSERT INTO products (name, category, size, price)
    VALUES (p_name, p_category, p_size, p_price);
    
    SELECT LAST_INSERT_ID() as product_id, 'Success' as status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_profit` (IN `p_source` VARCHAR(255), IN `p_amount` DECIMAL(10,2), IN `p_date` DATE, IN `p_notes` TEXT)   BEGIN
    INSERT INTO profits (source, amount, date, notes)
    VALUES (p_source, p_amount, p_date, p_notes);
    
    SELECT LAST_INSERT_ID() as profit_id, 'Success' as status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_authenticate_user` (IN `p_username` VARCHAR(100))   BEGIN
    SELECT admin_id, username, password, phone_number
    FROM admin
    WHERE username = p_username
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_expense` (IN `p_expense_id` INT)   BEGIN
    DELETE FROM expenses WHERE expense_id = p_expense_id;
    SELECT ROW_COUNT() as affected_rows, 'Deleted' as status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_profit` (IN `p_profit_id` INT)   BEGIN
    DELETE FROM profits WHERE profit_id = p_profit_id;
    SELECT ROW_COUNT() as affected_rows, 'Deleted' as status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_all_products` ()   BEGIN
    SELECT product_id, name, category, size, price
    FROM products
    ORDER BY category, name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_dashboard_stats` ()   BEGIN
    SELECT 
        (SELECT COALESCE(SUM(amount), 0) FROM profits WHERE date = CURDATE()) as today_sales,
        (SELECT COALESCE(SUM(amount), 0) FROM profits WHERE YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE())) as month_profit,
        (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE())) as month_expense,
        (SELECT COUNT(*) FROM products) as product_count;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_expenses_by_date` (IN `p_date` DATE)   BEGIN
    SELECT expense_id, category, amount, date, notes
    FROM expenses
    WHERE date = p_date
    ORDER BY date DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_monthly_summary` (IN `p_year` INT, IN `p_month` INT)   BEGIN
    SELECT 
        (SELECT COALESCE(SUM(amount), 0) FROM profits WHERE YEAR(date) = p_year AND MONTH(date) = p_month) as total_profit,
        (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE YEAR(date) = p_year AND MONTH(date) = p_month) as total_expense,
        (SELECT COALESCE(SUM(amount), 0) FROM profits WHERE YEAR(date) = p_year AND MONTH(date) = p_month) - 
        (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE YEAR(date) = p_year AND MONTH(date) = p_month) as net_income;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_profits_by_date` (IN `p_date` DATE)   BEGIN
    SELECT profit_id, source, amount, date, notes
    FROM profits
    WHERE date = p_date
    ORDER BY date DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_test` ()   BEGIN
    SELECT 'Hello from stored procedure!' as message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_password` (IN `p_phone` VARCHAR(20), IN `p_new_password` VARCHAR(255))   BEGIN
    UPDATE admin
    SET password = p_new_password
    WHERE phone_number = p_phone;
    
    SELECT ROW_COUNT() as affected_rows, 'Success' as status;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `phone_number`) VALUES
(6, '1028_cafe', '$2y$10$VjT9hhHdu0fjopddbnya9uktq2Cy2NkItS4ca4BVrHKudtZXs5juW', '09941425046');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `category`, `description`, `amount`, `date`) VALUES
(3, 'Rent', NULL, 5000.00, '2025-11-21'),
(4, 'Others', NULL, 1500.00, '2025-11-21');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `drive_link` varchar(255) DEFAULT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `size` varchar(50) NOT NULL DEFAULT 'One Size',
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `date_added` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category`, `size`, `price`, `stock`, `date_added`) VALUES
(2, 'Spanish Latte', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(3, 'Wintermelon', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(4, 'Dark Chocolate', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(5, 'Okinawa', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(6, 'Salted Caramel', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(7, 'Taro', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(8, 'Red Velvet', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(9, 'Matcha', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(10, 'Cookies and Cream', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(11, 'Choco Strawberry', 'Milk Tea', 'Medium', 28.00, 0, '2025-11-21'),
(12, 'Wintermelon', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(13, 'Dark Chocolate', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(14, 'Okinawa', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(15, 'Salted Caramel', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(16, 'Taro', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(17, 'Red Velvet', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(18, 'Matcha', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(19, 'Cookies and Cream', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(20, 'Choco Strawberry', 'Milk Tea', 'Large', 38.00, 0, '2025-11-21'),
(21, 'Wintermelon', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(22, 'Okinawa', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(23, 'Dark Chocolate', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(24, 'Matcha', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(25, 'Red Velvet', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(26, 'Double Oreo', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(27, 'Choco Strawberry', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(28, 'Salted Caramel', 'Cheesecake', 'Medium', 43.00, 0, '2025-11-21'),
(29, 'Wintermelon', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(30, 'Okinawa', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(31, 'Dark Chocolate', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(32, 'Matcha', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(33, 'Red Velvet', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(34, 'Double Oreo', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(35, 'Choco Strawberry', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(36, 'Salted Caramel', 'Cheesecake', 'Large', 53.00, 0, '2025-11-21'),
(37, 'Dark Caramel CB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(38, 'Dark Mocha CB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(39, 'Java Chips CB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(40, 'Matcha NCB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(41, 'Triple Chocolate NCB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(42, 'Dark Chocolate Berry NCB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(43, 'Strawberries and Cream NCB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(44, 'Blueberries and Cream NCB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(45, 'Mix Berries NCB', 'Regular Frappe', 'Medium', 45.00, 0, '2025-11-21'),
(46, 'Dark Caramel CB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(47, 'Dark Mocha CB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(48, 'Java Chips CB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(49, 'Matcha NCB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(50, 'Triple Chocolate NCB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(51, 'Dark Chocolate Berry NCB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(52, 'Strawberries and Cream NCB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(53, 'Blueberries and Cream NCB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(54, 'Mix Berries NCB', 'Regular Frappe', 'Large', 55.00, 0, '2025-11-21'),
(55, 'Dark Chocolate Lava', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(56, 'Red Velvet Cream Cheese', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(57, 'Kopi Caramel', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(58, 'Dark Forest', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(59, 'Mango Cheesecake', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(60, 'Lava Cheesecake', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(61, 'Strawberry Cheesecake', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(62, 'Oreo Cheesecake', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(63, 'White Choco Mocha', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(64, 'Dark Choco Creamcheese', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(65, 'Ube Cream Cheese', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(66, 'Mint Chocolate Cream', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(67, 'Almond Matcha', 'Premium Frappe', 'One Size', 88.00, 0, '2025-11-21'),
(68, 'Lychee', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(69, 'Strawberry', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(70, 'Green Apple', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(71, 'Lemon', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(72, 'Blueberry', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(73, 'Kiwi', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(74, 'Mango', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(75, 'Passion Fruit', 'Fruit Tea', 'Medium', 35.00, 0, '2025-11-21'),
(76, 'Lychee', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(77, 'Strawberry', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(78, 'Green Apple', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(79, 'Lemon', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(80, 'Blueberry', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(81, 'Kiwi', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(82, 'Mango', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(83, 'Passion Fruit', 'Fruit Tea', 'Large', 45.00, 0, '2025-11-21'),
(84, 'Iced Americano', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(85, 'Spanish Latte', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(86, 'White Chocolate', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(87, 'Mocha Latte', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(88, 'Caramel Macchiato', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(89, 'White Mocha', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(90, 'French Vanilla', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(91, 'Salted Caramel Latte', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(92, 'Dirty Matcha', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(93, 'Peppermint Mocha', 'Iced Coffee', 'Medium', 50.00, 0, '2025-11-21'),
(94, 'Iced Americano', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(95, 'Spanish Latte', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(96, 'White Chocolate', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(97, 'Mocha Latte', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(98, 'Caramel Macchiato', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(99, 'White Mocha', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(100, 'French Vanilla', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(101, 'Salted Caramel Latte', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(102, 'Dirty Matcha', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(103, 'Peppermint Mocha', 'Iced Coffee', 'Large', 60.00, 0, '2025-11-21'),
(104, 'Caramel Macchiato', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(105, 'Salted Caramel Latte', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(106, 'Spanish Latte', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(107, 'White Chocolate', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(108, 'Mocha Latte', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(109, 'French Vanilla', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(110, 'White Mocha', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(111, 'Hot Chocolate', 'Hot Drinks', 'One Size', 45.00, 0, '2025-11-21'),
(112, 'Strawberry and Milk', 'Milk Series', 'Medium', 55.00, 0, '2025-11-21'),
(113, 'Mango and Milk', 'Milk Series', 'Medium', 55.00, 0, '2025-11-21'),
(114, 'Blueberries and Milk', 'Milk Series', 'Medium', 55.00, 0, '2025-11-21'),
(115, 'Ube Matcha and Milk', 'Milk Series', 'Medium', 55.00, 0, '2025-11-21'),
(116, 'Strawberry and Milk', 'Milk Series', 'Large', 65.00, 0, '2025-11-21'),
(117, 'Mango and Milk', 'Milk Series', 'Large', 65.00, 0, '2025-11-21'),
(118, 'Blueberries and Milk', 'Milk Series', 'Large', 65.00, 0, '2025-11-21'),
(119, 'Ube Matcha and Milk', 'Milk Series', 'Large', 65.00, 0, '2025-11-21'),
(120, 'Green Berry', 'Fruit Soda', 'One Size', 60.00, 0, '2025-11-21'),
(121, 'Mango Berry', 'Fruit Soda', 'One Size', 60.00, 0, '2025-11-21');

-- --------------------------------------------------------

--
-- Table structure for table `profits`
--

CREATE TABLE `profits` (
  `profit_id` int(11) NOT NULL,
  `source` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profits`
--

INSERT INTO `profits` (`profit_id`, `source`, `category`, `product_name`, `quantity`, `amount`, `date`, `notes`) VALUES
(1, 'Milktea', '', '', 1, 1000.00, '2025-11-16', 'hello'),
(2, 'Milktea', '', '', 1, 1000.00, '2025-11-16', 'hello'),
(3, 'Milk Tea', '', '', 1, 245.00, '2025-11-16', 'hreli'),
(4, 'Milk Tea', '', '', 1, 245.00, '2025-11-16', 'hreli'),
(5, 'Milk Tea', '', '', 1, 2500.00, '2025-11-16', ''),
(6, 'Cheesecake', '', '', 1, 100.00, '2025-11-16', ''),
(7, 'Milk Tea', '', '', 1, 10.00, '2025-11-16', ''),
(8, 'Iced Coffee', '', '', 1, 12345.00, '2025-11-16', ''),
(9, 'Fruit Soda', '', '', 1, 12345.00, '2025-11-16', ''),
(15, 'Iced Coffee', '', '', 1, 1000.00, '2025-11-21', 'Spanish Latte'),
(16, 'Fruit Tea', '', '', 1, 1500.00, '2025-11-21', 'Apple'),
(17, 'Milk Tea', '', 'Wintermelon', 1, 50.00, '2025-11-24', ''),
(18, 'Cheesecake', '', 'Blueberry Cheesecake', 1, 70.00, '2025-11-24', ''),
(19, 'Regular Frappe', '', 'Red Velvet', 1, 70.00, '2025-11-24', ''),
(20, 'Hot Drinks', '', 'Americano', 1, 500.00, '2025-11-24', ''),
(21, 'Regular Frappe', '', 'Double Chocolate', 1, 1000.00, '2025-11-25', ''),
(22, 'Milk Tea', '', 'Wintermelon', 1, 60.00, '2025-11-27', ''),
(23, 'Regular Frappe', '', 'Oreo Cookies', 1, 9999.00, '2025-11-28', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `profits`
--
ALTER TABLE `profits`
  ADD PRIMARY KEY (`profit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `profits`
--
ALTER TABLE `profits`
  MODIFY `profit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
