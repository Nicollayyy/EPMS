<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./html/login.html");
    exit();
}

// Load Google API client
require __DIR__ . '/vendor/autoload.php';

// Success / Error messages
$success = "";
$error = "";

// Google Drive setup
$client = new \Google_Client();
$client->setAuthConfig(__DIR__ . '/credentials.json'); // your service account JSON
$client->addScope(Google_Service_Drive::DRIVE_FILE);

$service = new Google_Service_Drive($client);

// Your Shared Drive folder ID
$folderId = '1pMDotLBHWMtZCyltJ54HDOdJHbxibbMt'; // replace with your actual Shared Drive folder ID

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];

    // Allowed file types
    $allowed_types = ['jpg','jpeg','png','pdf','doc','docx','xlsx','txt'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_types)) {
        $error = "File type not allowed. Allowed: " . implode(', ', $allowed_types);
    } else {
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $file_name,
            'parents' => [$folderId]
        ]);

        $content = file_get_contents($file_tmp);

        try {
            $file = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => mime_content_type($file_tmp),
                'uploadType' => 'multipart',
                'fields' => 'id,name',
                'supportsAllDrives' => true // important for Shared Drives
            ]);
            $success = "File uploaded successfully: " . $file->name;
        } catch (Exception $e) {
            $error = "Error uploading to Google Drive: " . $e->getMessage();
        }
    }
}

// Fetch files from Shared Drive folder
$files = [];
try {
    $optParams = [
        'fields' => 'files(id,name)',
        'q' => "'$folderId' in parents",
        'supportsAllDrives' => true,
        'includeItemsFromAllDrives' => true
    ];
    $driveFiles = $service->files->listFiles($optParams);
    $files = $driveFiles->getFiles();
} catch (Exception $e) {
    $error = "Error fetching files from Drive: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload | 1028 Café</title>
    <style>
        body { margin:0; font-family:Arial,sans-serif; background:#f5f5f5; }
        .sidebar { width:220px; background:#333; color:#fff; position:fixed; top:0; bottom:0; padding:20px; }
        .sidebar .brand { font-size:24px; font-weight:bold; margin-bottom:30px; }
        .sidebar .nav { display:flex; flex-direction:column; }
        .sidebar .nav-item { color:#fff; padding:10px; text-decoration:none; margin-bottom:5px; border-radius:4px; }
        .sidebar .nav-item.active { background:#555; }
        .sidebar-footer { position:absolute; bottom:20px; }
        .main-content { margin-left:240px; padding:20px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .topbar h2 { margin:0; }
        .topbar .user-info a { text-decoration:none; color:#333; font-weight:bold; }
        .form-card { background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); max-width:500px; margin:auto 0 30px; }
        .form-card h3 { margin-top:0; margin-bottom:20px; font-size:22px; color:#333; }
        .form-card input[type="file"], .form-card input[type="submit"] { width:100%; padding:10px; margin-bottom:15px; border-radius:5px; font-size:14px; }
        .form-card input[type="submit"] { background:#333; color:#fff; border:none; cursor:pointer; transition:0.3s; }
        .form-card input[type="submit"]:hover { background:#555; }
        .success-message, .error-message { text-align:center; padding:10px; margin-bottom:15px; border-radius:5px; font-weight:bold; }
        .success-message { background:#d4edda; color:#155724; }
        .error-message { background:#f8d7da; color:#721c24; }
        table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-top:20px; }
        table th, table td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
        table th { background:#f0f0f0; }
        a.download-link { color:#333; text-decoration:none; font-weight:bold; }
        a.download-link:hover { text-decoration:underline; }
        @media screen and (max-width:768px){ .main-content{margin-left:0; padding:15px;} .sidebar{width:100%; position:relative; height:auto;} .form-card{margin:20px;} }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand">1028 Café</div>
    <nav class="nav">
        <a href="dashboard.php" class="nav-item active">Dashboard</a>
            <div class="nav-section">Profit</div>
            <a href="./../html/profit_add.html" class="nav-item">Add Profit</a>
            <a href="profit_list.php" class="nav-item">Profit List</a>
            <div class="nav-section">Expense</div>
            <a href="expense_add.php" class="nav-item">Add Expense</a>
            <a href="expense_list.php" class="nav-item">Expense List</a>
            <div class="nav-section">Products</div>
            <a href="product_add.php" class="nav-item">Add Product</a>
            <a href="product_list.php" class="nav-item">Products</a>
            <div class="nav-section">Files</div>
            <a href="file_upload.php" class="nav-item">Upload File</a>
            <a href="file_list.php" class="nav-item">File List</a>
    </nav>
    <div class="sidebar-footer">
        Logged in as <?= htmlspecialchars($_SESSION['username']) ?>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
    <header class="topbar">
        <h2>Upload File</h2>
        <div class="user-info">
            Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="form-card">
        <h3>Upload a File</h3>
        <?php if($success): ?><div class="success-message"><?= $success ?></div><?php endif; ?>
        <?php if($error): ?><div class="error-message"><?= $error ?></div><?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <input type="submit" value="Upload">
        </form>
    </div>

    <?php if(!empty($files)): ?>
    <table>
        <tr><th>File Name</th><th>Action</th></tr>
        <?php foreach($files as $file): ?>
            <tr>
                <td><?= htmlspecialchars($file->name) ?></td>
                <td><a class="download-link" href="https://drive.google.com/uc?id=<?= $file->id ?>&export=download" target="_blank">Download</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</main>

</body>
</html>
