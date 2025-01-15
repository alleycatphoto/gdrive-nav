<?php
// Get file ID from URL
$fileId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$fileId) {
    header("Location: /");
    exit;
}

try {
    $driveService = new \App\Services\DriveService();
    $file = $driveService->getFileMetadata($fileId);

    if (!$file) {
        throw new Exception("File not found");
    }

    // Determine file type and set appropriate meta tags
    $mimeType = $file['mimeType'];
    $isImage = strpos($mimeType, 'image/') === 0;
    $isVideo = strpos($mimeType, 'video/') === 0;
    $isPDF = $mimeType === 'application/pdf';
    
    // Generate proxy URL for media content
    $proxyUrl = "/proxy/" . $fileId;
    
    // Set title and description
    $title = $file['name'];
    $description = "Shared via DNA Distribution Customer Resources";
    
} catch (Exception $e) {
    error_log("Error in share view: " . $e->getMessage());
    header("Location: /");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - DNA Distribution</title>
    
    <!-- OpenGraph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:site_name" content="DNA Distribution Customer Resources">
    
    <?php if ($isImage): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($proxyUrl); ?>">
        <meta property="og:type" content="image">
    <?php elseif ($isVideo): ?>
        <meta property="og:video" content="<?php echo htmlspecialchars($proxyUrl); ?>">
        <meta property="og:video:type" content="<?php echo htmlspecialchars($mimeType); ?>">
        <meta property="og:type" content="video">
    <?php elseif ($isPDF): ?>
        <meta property="og:type" content="article">
        <meta property="og:image" content="/attached_assets/pdf_preview.png">
    <?php else: ?>
        <meta property="og:type" content="article">
    <?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
    <?php include __DIR__ . '/../includes/styles.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/../includes/nav.php'; ?>
    
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4"><?php echo htmlspecialchars($title); ?></h4>
                        
                        <?php if ($isImage): ?>
                            <img src="<?php echo htmlspecialchars($proxyUrl); ?>" 
                                 alt="<?php echo htmlspecialchars($title); ?>" 
                                 class="img-fluid rounded mb-3">
                        <?php elseif ($isVideo): ?>
                            <video controls class="w-100 rounded mb-3">
                                <source src="<?php echo htmlspecialchars($proxyUrl); ?>" 
                                        type="<?php echo htmlspecialchars($mimeType); ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php elseif ($isPDF): ?>
                            <div class="ratio ratio-16x9 mb-3">
                                <iframe src="<?php echo htmlspecialchars($proxyUrl); ?>" 
                                        title="<?php echo htmlspecialchars($title); ?>" 
                                        allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="<?php echo htmlspecialchars($proxyUrl); ?>" 
                               class="btn btn-primary" 
                               download="<?php echo htmlspecialchars($title); ?>">
                                <i class="fas fa-download me-2"></i>Download
                            </a>
                            <a href="/" class="btn btn-secondary">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/scripts.php'; ?>
</body>
</html>
