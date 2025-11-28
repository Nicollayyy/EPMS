<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.html");
    exit();
}

include __DIR__ . '/../database/db.php';

// Fetch files from database
$stmt = $conn->prepare("SELECT file_id, file_name, file_path, drive_link, upload_date FROM files ORDER BY upload_date DESC");
$stmt->execute();
$result = $stmt->get_result();
$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>1028 Tea & Café — File Storage</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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

      <div id="alertContainer"></div>

      <div class="row g-4">
        <!-- Upload Section -->
        <div class="col-12 col-lg-4">
          <div class="panel p-4">
            <div class="panel-head mb-4">
              <h4 class="mb-1"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload File</h4>
              <small class="text-muted">Upload files to Google Drive</small>
            </div>

            <form id="uploadForm" enctype="multipart/form-data">
              <div class="mb-4">
                <label for="file" class="form-label fw-semibold">Select File <span class="text-danger">*</span></label>
                <input type="file" name="file" id="file" class="form-control form-control-lg" required>
                <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX, PPT, TXT</small>
              </div>

              <button type="submit" class="btn btn-gold w-100 btn-lg" id="uploadBtn">
                <i class="fa-solid fa-upload me-2"></i>Upload File
              </button>
            </form>
          </div>

          <div class="panel p-4 mt-4">
            <div class="panel-head mb-3">
              <h5 class="mb-1"><i class="fa-solid fa-info-circle me-2"></i>Storage Info</h5>
            </div>
            <div class="small">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Total Files:</span>
                <strong><?= count($files) ?></strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Storage:</span>
                <strong>Google Drive</strong>
              </div>
            </div>
          </div>
        </div>

        <!-- Files List -->
        <div class="col-12 col-lg-8">
          <div class="panel p-4">
            <div class="panel-head mb-4">
              <h4 class="mb-1"><i class="fa-solid fa-folder-open me-2"></i>Files</h4>
              <small class="text-muted"><?= count($files) ?> file(s) stored</small>
            </div>

            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th><i class="fa-solid fa-file me-2"></i>File Name</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="filesTable">
                  <!-- Files will be loaded here -->
                </tbody>
              </table>
            </div>
            <div id="emptyState" class="text-center py-5" style="display: none;">
              <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">No files uploaded yet</h5>
              <p class="text-muted">Upload your first file to get started</p>
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

    // Profile dropdown
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');
    profileToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle('show');
    });
    document.addEventListener('click', () => profileMenu.classList.remove('show'));

    // Load files
    function loadFiles() {
      fetch('../fetch_files.php')
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            displayFiles(data.files);
          }
        });
    }

    // Display files
    function displayFiles(files) {
      const tbody = document.getElementById('filesTable');
      const emptyState = document.getElementById('emptyState');
      const fileCount = document.getElementById('fileCount');
      
      if (files.length === 0) {
        tbody.innerHTML = '';
        emptyState.style.display = 'block';
        if (fileCount) fileCount.textContent = '0';
        return;
      }
      
      emptyState.style.display = 'none';
      if (fileCount) fileCount.textContent = files.length;
      
      tbody.innerHTML = files.map(file => {
        const ext = file.file_name.split('.').pop().toLowerCase();
        let icon = 'fa-file';
        if (['jpg','jpeg','png','gif'].includes(ext)) icon = 'fa-file-image';
        else if (ext === 'pdf') icon = 'fa-file-pdf';
        else if (['doc','docx'].includes(ext)) icon = 'fa-file-word';
        else if (['xls','xlsx'].includes(ext)) icon = 'fa-file-excel';
        else if (ext === 'txt') icon = 'fa-file-lines';
        
        const date = new Date(file.upload_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'});
        
        return `
          <tr>
            <td><i class="fa-solid ${icon} me-2 text-muted"></i>${file.file_name}</td>
            <td><span class="badge bg-secondary">${ext.toUpperCase()}</span></td>
            <td>${date}</td>
            <td>
              <a href="../${file.file_path}" class="btn btn-sm btn-primary" download>
                <i class="fa-solid fa-download me-1"></i>Download
              </a>
              ${file.drive_link ? `<a href="${file.drive_link}" class="btn btn-sm btn-info" target="_blank">
                <i class="fa-solid fa-cloud me-1"></i>Drive
              </a>` : ''}
              <button class="btn btn-sm btn-danger" onclick="deleteFile(${file.file_id}, '${file.file_name}')">
                <i class="fa-solid fa-trash me-1"></i>Delete
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    // Upload file
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const uploadBtn = document.getElementById('uploadBtn');
      uploadBtn.disabled = true;
      uploadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Uploading...';
      
      fetch('../file_handler.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        showAlert(data.success ? 'success' : 'danger', data.message);
        if (data.success) {
          this.reset();
          loadFiles();
        }
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fa-solid fa-upload me-2"></i>Upload File';
      });
    });

    // Delete file
    function deleteFile(id, name) {
      if (!confirm(`Are you sure you want to delete "${name}"?`)) return;
      
      const formData = new FormData();
      formData.append('delete_id', id);
      
      fetch('../file_handler.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        showAlert(data.success ? 'success' : 'danger', data.message);
        if (data.success) loadFiles();
      });
    }

    // Show alert
    function showAlert(type, message) {
      const alert = document.createElement('div');
      alert.className = `alert alert-${type} alert-dismissible fade show`;
      alert.innerHTML = `
        <i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-exclamation'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      document.getElementById('alertContainer').appendChild(alert);
      setTimeout(() => alert.remove(), 5000);
    }

    // Load files on page load
    loadFiles();
  </script>
</body>
</html>
