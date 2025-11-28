<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>1028 Tea & Café — Expenses</title>

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
              $message = 'Expense record added successfully!';
          } elseif ($_GET['success'] == 'deleted') {
              $message = 'Expense record deleted successfully!';
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
          <div class="card-hero p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h2 class="mb-1">Expense Management</h2>
                <p class="text-muted mb-0">Here's a summary of your expenses.</p>
              </div>
              <div class="text-end">
                <small class="text-muted">Updated: <span id="updatedAt">—</span></small>
              </div>
            </div>

            <div class="row gx-3 gy-3 mt-3">
              <div class="col-6 col-lg-4">
                <div class="stat-card p-3">
                  <div class="stat-label">Today's Expenses</div>
                  <div class="stat-value" id="todayExpense">₱ 0</div>
                  <div class="stat-meta text-danger"><i class="fa-solid fa-arrow-down"></i> 0.0%</div>
                </div>
              </div>

              <div class="col-6 col-lg-4">
                <div class="stat-card p-3">
                  <div class="stat-label">This Month Expenses</div>
                  <div class="stat-value" id="monthExpense">₱ 0</div>
                  <div class="stat-meta text-danger"><i class="fa-solid fa-arrow-down"></i> 0.0%</div>
                </div>
              </div>

              <div class="col-6 col-lg-4">
                <div class="stat-card p-3">
                  <div class="stat-label">Categories</div>
                  <div class="stat-value" id="categoryCount">0</div>
                  <div class="stat-meta text-muted">active</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Expense Table -->
          <div class="panel p-3">
            <div class="panel-head d-flex justify-content-between align-items-center mb-3">
              <strong>Recent Expenses</strong>
              <a href="./../expense_add.php" class="btn btn-gold btn-sm">
                <i class="fa-solid fa-plus me-2"></i>Add Expense
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="expenseTable">
                  <tr>
                    <td colspan="5" class="text-center">No expenses recorded yet</td>
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
                  <strong>Expenses by Category</strong>
                  <small class="text-muted">Current Month</small>
                </div>
                <div style="height: 280px; position: relative;">
                  <canvas id="expenseChart"></canvas>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <div class="panel p-3">
                <div class="panel-head d-flex justify-content-between align-items-center mb-2">
                  <strong>Daily Expenses</strong>
                  <small class="text-muted">Last 7 Days</small>
                </div>
                <div style="height: 280px; position: relative;">
                  <canvas id="dailyExpenseChart"></canvas>
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

    // Fetch and display expense data
    async function loadExpenseData() {
      try {
        const response = await fetch('../fetch_expense_data.php');
        const data = await response.json();

        // Update stats
        document.getElementById('todayExpense').textContent = '₱ ' + data.todayExpense.toLocaleString();
        document.getElementById('monthExpense').textContent = '₱ ' + data.monthExpense.toLocaleString();
        document.getElementById('categoryCount').textContent = data.categoryCount;

        // Update expense table
        const tableBody = document.getElementById('expenseTable');
        if (data.recentExpenses && data.recentExpenses.length > 0) {
          tableBody.innerHTML = '';
          data.recentExpenses.forEach((expense, index) => {
            const row = `
              <tr>
                <td>${index + 1}</td>
                <td><span class="badge bg-secondary">${expense.category}</span></td>
                <td class="fw-bold text-danger">₱ ${parseFloat(expense.amount).toLocaleString()}</td>
                <td>${expense.date}</td>
                <td>
                  <a href="../expense_edit.php?id=${expense.expense_id}" class="btn btn-sm btn-warning">Edit</a>
                  <a href="../expense_delete.php?id=${expense.expense_id}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
              </tr>
            `;
            tableBody.innerHTML += row;
          });
        }

        // Update expense chart with category breakdown
        const ctx = document.getElementById('expenseChart');
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

        // Update daily expense chart
        const dailyCtx = document.getElementById('dailyExpenseChart');
        if (data.dailyExpenses && data.dailyExpenses.length > 0) {
          const dailyLabels = data.dailyExpenses.map(d => d.date);
          const dailyValues = data.dailyExpenses.map(d => parseFloat(d.total));
          
          new Chart(dailyCtx, {
            type: 'bar',
            data: {
              labels: dailyLabels,
              datasets: [{
                label: 'Daily Expenses',
                data: dailyValues,
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: '#dc3545',
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
                      return 'Expenses: ₱' + context.parsed.y.toLocaleString();
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
          new Chart(dailyCtx, {
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
              plugins: { legend: { display: false } },
              scales: { y: { beginAtZero: true } }
            }
          });
        }

        document.getElementById('updatedAt').textContent = new Date().toLocaleString();

      } catch (error) {
        console.error('Error loading expense data:', error);
      }
    }

    // Load data on page load
    loadExpenseData();
  </script>
</body>
</html>
