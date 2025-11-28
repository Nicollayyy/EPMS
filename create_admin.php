<?php
// Make sure session is started before including this file
if (!isset($_SESSION)) session_start();
?>

<aside class="sidebar">
    <div class="brand">
        <div class="logo">1028</div>
        <div class="brand-name">1028 Café</div>
    </div>

    <nav class="nav">
        <a href="../dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>

        <div class="nav-section">Profit</div>
        <a href="../profit_add.php" class="nav-item">Add Profit</a>
        <a href="../profit_list.php" class="nav-item">Profit List</a>

        <div class="nav-section">Expense</div>
        <a href="../expense_add.php" class="nav-item">Add Expense</a>
        <a href="../expense_list.php" class="nav-item">Expense List</a>

        <div class="nav-section">Products</div>
        <a href="../product_add.php" class="nav-item">Add Product</a>
        <a href="../product_list.php" class="nav-item">Products</a>

        <div class="nav-section">Files</div>
        <a href="../file_upload.php" class="nav-item">Upload File</a>
        <a href="../file_list.php" class="nav-item">File List</a>
    </nav>

    <div class="sidebar-footer">
        <small>Logged in as</small>
        <div class="sidebar-user"><?= htmlspecialchars($_SESSION['username']) ?></div>
    </div>
</aside>
