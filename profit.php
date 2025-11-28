<?php
// profit.php
include 'db.php'; // your database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>1028 Tea & Café — Profit</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./../css/dashboard.css">
</head>
<body class="dash-body">

  <!-- Topbar -->
  <header class="topbar d-flex align-items-center justify-content-between px-3 shadow-sm">
    <div class="d-flex align-items-center gap-3">
      <button id="btnToggle" class="btn btn-ghost text-gold d-lg-none"><i class="fa-solid fa-bars fa-lg"></i></button>
      <div class="brand d-flex align-items-center gap-3">
        <img src="../assets/logo.jpg" alt="logo" class="logo">
        <div class="brand-text">
          <div class="brand-name">1028 Tea & Café</div>
          <small class="brand-sub">Expense & Profit Management</small>
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="me-3 text-muted small">Welcome, <strong>Admin</strong></div>
      <div class="profile position-relative">
        <button id="profileToggle" class="btn btn-outline-light rounded-circle p-2">
          <i class="fa-solid fa-user"></i>
        </button>
        <div id="profileMenu" class="dropdown-menu dropdown-menu-end shadow-sm py-2">
          <a class="dropdown-item" href="#">Profile</a>
          <a class="dropdown-item" href="../logout.php">Logout</a>
        </div>
      </div>
    </div>
  </header>

  <div class="app-shell d-flex">

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar bg-dark-brown shadow">
      <div class="sidebar-inner">
        <div class="sidebar-top px-3 py-3 border-bottom">
          <div class="sidebar-title text-white fw-semibold">MENU</div>
        </div>

        <ul class="nav flex-column mt-3 px-2">
          <li class="nav-item mb-1">
            <a href="dashboard.php" class="nav-link d-flex align-items-center gap-3">
              <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="profit.php" class="nav-link d-flex align-items-center gap-3 active">
              <i class="fa-solid fa-coins"></i><span>Profit</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="expenses.php" class="nav-link d-flex align-items-center gap-3">
              <i class="fa-solid fa-money-bill-transfer"></i><span>Expenses</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="products.php" class="nav-link d-flex align-items-center gap-3">
              <i class="fa-solid fa-box-open"></i><span>Products</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="file_manager.php" class="nav-link d-flex align-items-center gap-3">
              <i class="fa-solid fa-folder"></i><span>Files</span>
            </a>
          </li>
          <li class="nav-item mt-3">
            <a href="analytics.php" class="nav-link d-flex align-items-center gap-3">
              <i class="fa-solid fa-chart-simple"></i><span>Analytics</span>
            </a>
          </li>
        </ul>

        <div class="sidebar-footer mt-auto p-3">
          <small class="text-muted d-block">Logged in as</small>
          <div class="fw-bold text-white">ashmrls22</div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="content p-4">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Profit Records</h2>
        <a href="profit_add.php" class="btn btn-gold"><i class="fa-solid fa-plus me-2"></i>Add Profit</a>
      </div>

      <!-- Profit Table -->
      <div class="panel p-3">
        <table class="table table-striped table-hover text-dark">
          <thead>
            <tr>
              <th>#</th>
              <th>Category / Product</th>
              <th>Amount (₱)</th>
              <th>Date</th>
              <th>Notes</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT * FROM profits ORDER BY date DESC";
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                $i = 1;
                while($row = $result->fetch_assoc()){
                    echo "<tr>";
                    echo "<td>{$i}</td>";
                    echo "<td>{$row['source']}</td>";
                    echo "<td>" . number_format($row['amount'],2) . "</td>";
                    echo "<td>{$row['date']}</td>";
                    echo "<td>{$row['notes']}</td>";
                    echo "<td>
                            <a href='profit_edit.php?id={$row['profit_id']}' class='btn btn-sm btn-outline-primary me-1'>
                              <i class='fa-solid fa-pen'></i>
                            </a>
                            <a href='profit_delete.php?id={$row['profit_id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete this record?\")'>
                              <i class='fa-solid fa-trash'></i>
                            </a>
                          </td>";
                    echo "</tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No profit records found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar toggle -->
  <script>
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('sidebar');
    btnToggle?.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');
    profileToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle('show');
    });
    document.addEventListener('click', () => profileMenu.classList.remove('show'));
  </script>

</body>
</html>
