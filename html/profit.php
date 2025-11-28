<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>1028 Tea & Café — Profit</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./../css/dashboard.css">
</head>
<body class="dash-body">

  <!-- Include Topbar -->
  <?php include './../Includes/topbar.php'; ?>

  <div class="app-shell d-flex">

    <!-- Include Sidebar -->
    <?php include './../Includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content p-4">

      <?php
      // Display success messages
      if (isset($_GET['success'])) {
          $message = '';
          if ($_GET['success'] == '1') {
              $message = 'Sale record added successfully!';
          } elseif ($_GET['success'] == 'deleted') {
              $message = 'Sale record deleted successfully!';
          }
          if ($message) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="fa-solid fa-circle-check me-2"></i>' . $message . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
      }
      
      // Display error messages
      if (isset($_GET['error'])) {
          $message = '';
          if ($_GET['error'] == 'notfound') {
              $message = 'Record not found!';
          }
          if ($message) {
              echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <i class="fa-solid fa-circle-exclamation me-2"></i>' . $message . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
      }
      ?>

      <!-- Stats Row -->
      <div class="row g-3">
        <div class="col-12">
          <div class="card-hero p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h2 class="mb-1">Profit Management</h2>
                <p class="text-muted mb-0">Here's a summary of your profits.</p>
              </div>
              <div class="text-end">
                <small class="text-muted">Updated: <span id="updatedAt">—</span></small>
              </div>
            </div>

            <div class="row gx-3 gy-3 mt-3">
              <div class="col-6 col-md-4">
                <div class="stat-card p-3">
                  <div class="stat-label">Today's Profit</div>
                  <div class="stat-value" id="todayProfit">₱ 0</div>
                  <div class="stat-meta text-success"><i class="fa-solid fa-arrow-up"></i> 0.0%</div>
                </div>
              </div>

              <div class="col-6 col-md-4">
                <div class="stat-card p-3">
                  <div class="stat-label">This Month Profit</div>
                  <div class="stat-value" id="monthProfit">₱ 0</div>
                  <div class="stat-meta text-success"><i class="fa-solid fa-arrow-up"></i> 0.0%</div>
                </div>
              </div>

              <div class="col-6 col-md-4">
                <div class="stat-card p-3">
                  <div class="stat-label">Products</div>
                  <div class="stat-value" id="productCount">0</div>
                  <div class="stat-meta text-muted">active</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Profit Table -->
          <div class="panel p-3">
            <div class="panel-head d-flex justify-content-between align-items-center mb-3">
              <strong>Recent Sales</strong>
              <a href="./profit_add_dash.php" class="btn btn-gold btn-sm">
                <i class="fa-solid fa-plus me-2"></i>Add Sale
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="profitTable">
                  <tr>
                    <td colspan="7" class="text-center">No sales recorded yet</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Charts Row -->
          <div class="row gx-3 gy-3 mt-3">
            <div class="col-12 col-lg-8">
              <div class="panel p-3">
                <div class="panel-head d-flex justify-content-between align-items-center mb-2">
                  <strong>Profit by Category</strong>
                  <small class="text-muted">Current Month</small>
                </div>
                <div style="height: 280px; position: relative;">
                  <canvas id="profitChart"></canvas>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <div class="panel p-3">
                <div class="panel-head mb-2">
                  <strong>Sales - Last 30 Days</strong>
                </div>
                <div style="height: 280px; position: relative;">
                  <canvas id="dailySalesChart"></canvas>
                </div>
              </div>
            </div>
          </div>

        </div>


      </div>

      <footer class="mt-4 text-center text-muted small">
        © 1028 Tea & Café — Expense & Profit Management
      </footer>
    </main>

  </div>

  <!-- Bootstrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Page JS -->
  <script>
    // Sidebar toggle
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('sidebar');
    btnToggle?.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

    // Profile dropdown
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');
    profileToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle('show');
    });
    document.addEventListener('click', () => profileMenu.classList.remove('show'));

    let dailySalesChartInstance = null;

    // Fetch and display profit data
    async function loadProfitData(period = '7') {
      try {
        const response = await fetch(`../fetch_profit_data.php?period=${period}`);
        const data = await response.json();

        // Update stats
        document.getElementById('todayProfit').textContent = '₱ ' + data.todayProfit.toLocaleString();
        document.getElementById('monthProfit').textContent = '₱ ' + data.monthProfit.toLocaleString();
        document.getElementById('productCount').textContent = data.productCount;

        // Update profit table
        const tableBody = document.getElementById('profitTable');
        if (data.recentProfits && data.recentProfits.length > 0) {
          tableBody.innerHTML = '';
          data.recentProfits.forEach((profit, index) => {
            const row = `
              <tr>
                <td>${index + 1}</td>
                <td><span class="badge bg-secondary">${profit.source}</span></td>
                <td>${profit.product_name || '-'}</td>
                <td><strong>${profit.quantity || 1}</strong></td>
                <td class="fw-bold" style="color: #d4a017;">₱ ${parseFloat(profit.amount).toLocaleString()}</td>
                <td>${profit.date}</td>
                <td>
                  <a href="../profit_edit.php?id=${profit.profit_id}" class="btn btn-sm btn-warning">Edit</a>
                  <a href="../profit_delete.php?id=${profit.profit_id}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
              </tr>
            `;
            tableBody.innerHTML += row;
          });
        }

        // Update profit chart with category breakdown
        const ctx = document.getElementById('profitChart');
        if (data.categoryBreakdown && data.categoryBreakdown.length > 0) {
          const labels = data.categoryBreakdown.map(c => c.category);
          const values = data.categoryBreakdown.map(c => parseFloat(c.total));
          
          new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: labels,
              datasets: [{
                data: values,
                backgroundColor: [
                  '#ffd700',  // Gold
                  '#d4a017',  // Dark Gold
                  '#ffe44d',  // Light Gold
                  '#ffed4e',  // Gold Accent
                  '#f3d36b',  // Soft Gold
                  '#ffc107',  // Amber
                  '#ffb300',  // Deep Amber
                  '#ffa000',  // Orange Gold
                  '#ff8f00',  // Dark Orange Gold
                  '#ff6f00'   // Deep Orange Gold
                ],
                borderColor: '#fff',
                borderWidth: 2
              }]
            },
            options: { 
              responsive: true,
              maintainAspectRatio: false,
              plugins: { 
                legend: { position: 'bottom' },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return context.label + ': ₱' + context.parsed.toLocaleString();
                    }
                  }
                }
              } 
            }
          });
        } else {
          new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: ['No Data'],
              datasets: [{
                data: [1],
                backgroundColor: ['#e6e1d6']
              }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
          });
        }

        // Update daily sales chart
        const dailyCtx = document.getElementById('dailySalesChart');
        
        // Destroy existing chart if it exists
        if (dailySalesChartInstance) {
          dailySalesChartInstance.destroy();
        }
        
        if (data.dailySales && data.dailySales.length > 0) {
          const dailyLabels = data.dailySales.map(d => d.date);
          const dailyValues = data.dailySales.map(d => parseFloat(d.total));
          
          dailySalesChartInstance = new Chart(dailyCtx, {
            type: 'bar',
            data: {
              labels: dailyLabels,
              datasets: [{
                label: 'Daily Sales',
                data: dailyValues,
                backgroundColor: 'rgba(255, 215, 0, 0.7)',
                borderColor: '#ffd700',
                borderWidth: 2,
                borderRadius: 6
              }]
            },
            options: { 
              responsive: true,
              maintainAspectRatio: false,
              plugins: { 
                legend: { display: false },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return 'Sales: ₱' + context.parsed.y.toLocaleString();
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return '₱' + value.toLocaleString();
                    }
                  }
                }
              }
            }
          });
        } else {
          dailySalesChartInstance = new Chart(dailyCtx, {
            type: 'bar',
            data: {
              labels: ['No Data'],
              datasets: [{
                data: [0],
                backgroundColor: ['#e6e1d6']
              }]
            },
            options: { 
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              scales: { y: { beginAtZero: true } }
            }
          });
        }

        document.getElementById('updatedAt').textContent = new Date().toLocaleString();

      } catch (error) {
        console.error('Error loading profit data:', error);
      }
    }

    // Removed period selector - always load 30 days

    // Load data on page load
    loadProfitData();
  </script>
</body>
</html>
