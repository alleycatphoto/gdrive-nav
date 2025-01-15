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

    // Get folder path/name
    $breadcrumbs = $driveService->getBreadcrumbs($fileId);

    // Find first subfolder after "Home"
    $folderName = 'Resources';
    if (count($breadcrumbs) > 1) {
        // Get the first non-Home folder name
        foreach ($breadcrumbs as $crumb) {
            if ($crumb['name'] !== 'Home') {
                $folderName = $crumb['name'];
                break;
            }
        }
    }

    // Set title and description
    $title = $folderName . " - DNA Distribution";
    $description = "DNA Distribution Customer Resources";

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
    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- OpenGraph Meta Tags -->
    <meta property="fb:app_id" content="237755102741371">
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:site_name" content="DNA Distribution Customer Resources">
    <meta property="og:url" content="https://dnadistribution.us">

    <?php if ($isImage): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($file['thumbnails']['facebook']); ?>">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:type" content="image">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="<?php echo htmlspecialchars($file['thumbnails']['twitter']); ?>">
    <?php elseif ($isVideo): ?>
        <!-- Video specific meta tags -->
        <meta property="og:type" content="video.other">
        <meta property="og:video" content="<?php echo htmlspecialchars($proxyUrl); ?>">
        <meta property="og:video:url" content="<?php echo htmlspecialchars($proxyUrl); ?>">
        <meta property="og:video:type" content="<?php echo htmlspecialchars($mimeType); ?>">
        <meta property="og:video:width" content="<?php echo htmlspecialchars($file['videoMetadata']['width'] ?? 1280); ?>">
        <meta property="og:video:height" content="<?php echo htmlspecialchars($file['videoMetadata']['height'] ?? 720); ?>">
        <!-- Thumbnail for video -->
        <meta property="og:image" content="<?php echo htmlspecialchars($file['thumbnails']['facebook']); ?>">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="player">
        <meta name="twitter:image" content="<?php echo htmlspecialchars($file['thumbnails']['twitter']); ?>">
        <meta name="twitter:player:width" content="<?php echo htmlspecialchars($file['videoMetadata']['width'] ?? 1280); ?>">
        <meta name="twitter:player:height" content="<?php echo htmlspecialchars($file['videoMetadata']['height'] ?? 720); ?>">
    <?php elseif ($isPDF): ?>
        <meta property="og:type" content="article">
        <meta property="og:image" content="/attached_assets/pdf_preview.png">
    <?php else: ?>
        <meta property="og:type" content="article">
    <?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <h3 class="text-light"><?php echo htmlspecialchars($folderName); ?></h3>
                    <a class="text-muted" href="https://dnadistribution.us">DNA Distribution</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <?php if ($isImage): ?>
                            <img src="<?php echo htmlspecialchars($proxyUrl); ?>" 
                                 alt="<?php echo htmlspecialchars($file['name']); ?>" 
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
                                        title="<?php echo htmlspecialchars($file['name']); ?>" 
                                        allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="<?php echo htmlspecialchars($proxyUrl); ?>" 
                               class="btn btn-primary" 
                               download="<?php echo htmlspecialchars($file['name']); ?>">
                                <i class="fas fa-download me-2"></i>Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>