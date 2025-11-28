<?php
session_start();
include 'database/db.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Load Google API client
require __DIR__ . '/vendor/autoload.php';

$client = new \Google_Client();
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$service = new Google_Service_Drive($client);

$folderId = '1pMDotLBHWMtZCyltJ54HDOdJHbxibbMt';

// UPLOAD FILE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    
    $allowed_types = ['jpg','jpeg','png','pdf','doc','docx','xlsx','txt','xls','ppt','pptx'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'File type not allowed']);
        exit();
    }
    
    // Create uploads folder if not exists
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $unique_name = time() . '_' . $file_name;
    $file_path = $upload_dir . $unique_name;
    
    // Move file to local storage
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Upload to Google Drive as backup
        $drive_link = null;
        try {
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $file_name,
                'parents' => [$folderId]
            ]);
            
            $content = file_get_contents($file_path);
            $driveFile = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => mime_content_type($file_path),
                'uploadType' => 'multipart',
                'fields' => 'id',
                'supportsAllDrives' => true
            ]);
            
            $drive_link = 'https://drive.google.com/file/d/' . $driveFile->id . '/view';
        } catch (Exception $e) {
            // Continue even if Drive upload fails
            error_log("Drive upload failed: " . $e->getMessage());
        }
        
        // Save to database
        $stmt = $conn->prepare("INSERT INTO files (file_name, file_path, drive_link) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $file_name, $file_path, $drive_link);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    }
}

// DELETE FILE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $file_id = $_POST['delete_id'];
    
    // Get file info
    $stmt = $conn->prepare("SELECT file_path, drive_link FROM files WHERE file_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Delete local file
        if (file_exists($row['file_path'])) {
            unlink($row['file_path']);
        }
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM files WHERE file_id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found']);
    }
    $stmt->close();
}

$conn->close();
?>
