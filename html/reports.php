<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>1028 Tea & Café — Reports</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="./../css/dashboard.css">

  <style>
    .calendar-container {
      background: #fff;
      border-radius: 8px;
      padding: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }
    .calendar-header h5 {
      color: #302014 !important;
      font-weight: 600;
      margin: 0;
      font-size: 0.85rem;
    }
    .calendar-header button {
      padding: 2px 8px;
      font-size: 0.75rem;
    }
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 3px;
    }
    .calendar-day-header {
      text-align: center;
      font-weight: 600;
      color: #302014;
      padding: 4px 2px;
      font-size: 0.7rem;
    }
    .calendar-day {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s ease;
      background: #f8f9fa;
      color: #302014;
      font-weight: 500;
      font-size: 0.75rem;
      padding: 2px;
    }
    .calendar-day:hover {
      background: rgba(255, 215, 0, 0.2);
      transform: scale(1.05);
    }
    .calendar-day.selected {
      background: linear-gradient(135deg, #ffd700, #f3d36b);
      color: #302014;
      font-weight: 700;
    }
    .calendar-day.today {
      border: 2px solid #ffd700;
    }
    .calendar-day.other-month {
      color: #ccc;
      background: #fafafa;
    }
    .summary-card {
      background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(255,215,0,0.05));
      border-radius: 8px;
      padding: 12px;
      border: 1px solid rgba(255,215,0,0.3);
    }
    .summary-card h6 {
      color: #302014 !important;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 10px !important;
    }
    .summary-card small.text-muted {
      color: #302014 !important;
      font-weight: 600;
      font-size: 0.75rem;
    }
    .summary-card .fw-bold {
      color: #302014 !important;
      font-size: 0.95rem;
    }
    .summary-card .text-success {
      color: #28a745 !important;
    }
    .summary-card .text-danger {
      color: #dc3545 !important;
    }
  </style>
</head>
<body class="dash-body">

  <!-- Include Topbar -->
  <?php include './../Includes/topbar.php'; ?>

  <div class="app-shell d-flex">

    <!-- Include Sidebar -->
    <?php include './../Includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content p-4">

      <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1" style="color: #ffffff;">Financial Reports</h2>
          <p class="mb-0" style="color: #ffffff; opacity: 0.9;">View expense and profit records by date or date range</p>
        </div>
        <button id="exportPdfBtn" class="btn btn-gold" style="display: none;">
          <i class="fa-solid fa-file-pdf me-2"></i>Export to PDF
        </button>
      </div>

      <div class="row g-4">
        <!-- Calendar Section -->
        <div class="col-12 col-lg-4">

          <div class="calendar-container">
            <div class="calendar-header">
              <button id="prevMonth" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-chevron-left"></i>
              </button>
              <h5 class="mb-0" id="currentMonth">November 2025</h5>
              <button id="nextMonth" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-chevron-right"></i>
              </button>
            </div>
            <div class="calendar-grid" id="calendarGrid">
              <!-- Calendar will be generated here -->
            </div>
          </div>

          <!-- Date Range Selector -->
          <div class="mt-3 p-3" style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h6 style="color: #302014; font-size: 0.85rem; margin-bottom: 10px;">
              <i class="fa-solid fa-calendar-range me-2"></i>Date Range
            </h6>
            <div class="mb-2">
              <label style="font-size: 0.75rem; color: #302014;">From:</label>
              <input type="date" id="startDate" class="form-control form-control-sm">
            </div>
            <div class="mb-2">
              <label style="font-size: 0.75rem; color: #302014;">To:</label>
              <input type="date" id="endDate" class="form-control form-control-sm">
            </div>
            <button id="applyRange" class="btn btn-sm btn-gold w-100 mb-1">
              <i class="fa-solid fa-check me-1"></i>Apply Range
            </button>
            <button id="clearRange" class="btn btn-sm btn-outline-secondary w-100">
              <i class="fa-solid fa-times me-1"></i>Clear
            </button>
          </div>

          <!-- Summary Card -->
          <div class="summary-card mt-4">
            <h6 class="mb-3"><i class="fa-solid fa-calendar-day me-2"></i>Selected Period Summary</h6>
            <div class="mb-2">
              <small class="text-muted">Period:</small>
              <div class="fw-bold" id="selectedDate">Select a date or range</div>
            </div>
            <div class="mb-2">
              <small class="text-muted">Total Profit:</small>
              <div class="fw-bold text-success" id="summaryProfit">₱ 0</div>
            </div>
            <div>
              <small class="text-muted">Total Expense:</small>
              <div class="fw-bold text-danger" id="summaryExpense">₱ 0</div>
            </div>
          </div>
        </div>

        <!-- Records Section -->
        <div class="col-12 col-lg-8">
          <!-- Combined Records -->
          <div class="panel p-4">
            <div class="panel-head mb-3">
              <h5 class="mb-1"><i class="fa-solid fa-chart-line me-2 text-warning"></i>Financial Records</h5>
              <small class="text-muted" id="totalCount">0 records</small>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody id="recordsTable">
                  <tr>
                    <td colspan="5" class="text-center text-muted">Select a date to view records</td>
                  </tr>
                </tbody>
              </table>
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

  <script>
    // Sidebar toggle
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('sidebar');
    btnToggle?.addEventListener('click', () => sidebar.classList.toggle('collapsed'));

    // Calendar variables
    let currentDate = new Date();
    let selectedDate = null;

    // Generate calendar
    function generateCalendar(year, month) {
      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      const prevLastDay = new Date(year, month, 0);
      const firstDayIndex = firstDay.getDay();
      const lastDayIndex = lastDay.getDay();
      const nextDays = 7 - lastDayIndex - 1;

      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                          'July', 'August', 'September', 'October', 'November', 'December'];
      
      document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;

      const calendarGrid = document.getElementById('calendarGrid');
      calendarGrid.innerHTML = '';

      // Day headers
      const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      dayHeaders.forEach(day => {
        const header = document.createElement('div');
        header.className = 'calendar-day-header';
        header.textContent = day;
        calendarGrid.appendChild(header);
      });

      // Previous month days
      for (let i = firstDayIndex; i > 0; i--) {
        const day = document.createElement('div');
        day.className = 'calendar-day other-month';
        day.textContent = prevLastDay.getDate() - i + 1;
        calendarGrid.appendChild(day);
      }

      // Current month days
      const today = new Date();
      for (let i = 1; i <= lastDay.getDate(); i++) {
        const day = document.createElement('div');
        day.className = 'calendar-day';
        day.textContent = i;
        
        const dayDate = new Date(year, month, i);
        if (dayDate.toDateString() === today.toDateString()) {
          day.classList.add('today');
        }

        day.addEventListener('click', () => {
          document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
          day.classList.add('selected');
          selectedDate = new Date(year, month, i);
          loadRecordsForDate(selectedDate);
        });

        calendarGrid.appendChild(day);
      }

      // Next month days
      for (let i = 1; i <= nextDays; i++) {
        const day = document.createElement('div');
        day.className = 'calendar-day other-month';
        day.textContent = i;
        calendarGrid.appendChild(day);
      }
    }

    // Format date helper
    function formatDate(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }

    // Load records for selected date or date range
    async function loadRecordsForDate(date, endDate = null) {
      const dateStr = formatDate(date);
      let url = `../fetch_reports_data.php?date=${dateStr}`;
      
      if (endDate) {
        const endDateStr = formatDate(endDate);
        url += `&endDate=${endDateStr}`;
        document.getElementById('selectedDate').textContent = 
          `${date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} - ${endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
        updateExportButton(dateStr, endDateStr);
      } else {
        document.getElementById('selectedDate').textContent = date.toLocaleDateString('en-US', { 
          weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        });
        updateExportButton(dateStr);
      }

      try {
        console.log('Fetching from URL:', url);
        const response = await fetch(url);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        const data = JSON.parse(text);
        console.log('Data received:', data);

        // Update summary
        document.getElementById('summaryProfit').textContent = '₱ ' + data.totalProfit.toLocaleString();
        document.getElementById('summaryExpense').textContent = '₱ ' + data.totalExpense.toLocaleString();

        // Combine profit and expense records
        const recordsTable = document.getElementById('recordsTable');
        const allRecords = [];
        
        // Add profits
        data.profits.forEach(profit => {
          allRecords.push({
            type: 'Profit',
            description: profit.source + (profit.notes ? ` (${profit.notes})` : ''),
            amount: parseFloat(profit.amount),
            date: profit.date,
            isProfit: true
          });
        });
        
        // Add expenses
        data.expenses.forEach(expense => {
          allRecords.push({
            type: 'Expense',
            description: expense.category,
            amount: parseFloat(expense.amount),
            date: expense.date,
            isProfit: false
          });
        });
        
        // Sort by date (newest first)
        allRecords.sort((a, b) => new Date(b.date) - new Date(a.date));
        
        // Update total count
        document.getElementById('totalCount').textContent = `${allRecords.length} record${allRecords.length !== 1 ? 's' : ''}`;
        
        // Display combined records
        if (allRecords.length > 0) {
          recordsTable.innerHTML = allRecords.map((record, index) => `
            <tr>
              <td>${index + 1}</td>
              <td><span class="badge ${record.isProfit ? 'bg-success' : 'bg-danger'}">${record.type}</span></td>
              <td>${record.description}</td>
              <td class="fw-bold ${record.isProfit ? 'text-success' : 'text-danger'}">₱ ${record.amount.toLocaleString()}</td>
              <td>${record.date}</td>
            </tr>
          `).join('');
        } else {
          recordsTable.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No records for this date</td></tr>';
        }

      } catch (error) {
        console.error('Error loading records:', error);
        console.error('Error details:', error.message);
        alert('Error loading records: ' + error.message + '\nCheck console for details.');
        
        // Show error in table
        document.getElementById('recordsTable').innerHTML = 
          '<tr><td colspan="5" class="text-center text-danger">Error loading data: ' + error.message + '</td></tr>';
      }
    }

    // Calendar navigation
    document.getElementById('prevMonth').addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });

    // Date range functionality
    document.getElementById('applyRange').addEventListener('click', () => {
      const startDateInput = document.getElementById('startDate').value;
      const endDateInput = document.getElementById('endDate').value;

      if (!startDateInput || !endDateInput) {
        alert('Please select both start and end dates');
        return;
      }

      const startDate = new Date(startDateInput + 'T00:00:00');
      const endDate = new Date(endDateInput + 'T00:00:00');

      if (startDate > endDate) {
        alert('Start date must be before end date');
        return;
      }

      // Clear calendar selection
      document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
      
      loadRecordsForDate(startDate, endDate);
    });

    document.getElementById('clearRange').addEventListener('click', () => {
      document.getElementById('startDate').value = '';
      document.getElementById('endDate').value = '';
      document.getElementById('selectedDate').textContent = 'Select a date or range';
      document.getElementById('summaryProfit').textContent = '₱ 0';
      document.getElementById('summaryExpense').textContent = '₱ 0';
      document.getElementById('recordsTable').innerHTML = '<tr><td colspan="5" class="text-center text-muted">Select a date to view records</td></tr>';
      document.getElementById('totalCount').textContent = '0 records';
      document.getElementById('exportPdfBtn').style.display = 'none';
      
      // Clear calendar selection
      document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
    });

    // Export to PDF functionality
    let currentExportDate = null;
    let currentExportEndDate = null;
    
    // Update export button visibility when date is selected
    function updateExportButton(date, endDate = null) {
      currentExportDate = date;
      currentExportEndDate = endDate;
      document.getElementById('exportPdfBtn').style.display = 'block';
    }
    
    document.getElementById('exportPdfBtn').addEventListener('click', () => {
      if (currentExportDate) {
        let url = `../generate_report_pdf.php?date=${currentExportDate}`;
        if (currentExportEndDate) {
          url += `&endDate=${currentExportEndDate}`;
        }
        window.open(url, '_blank');
      }
    });

    // Initialize calendar
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
  </script>
</body>
</html>
