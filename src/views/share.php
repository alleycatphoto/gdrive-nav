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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- OpenGraph Meta Tags -->
    <meta property="fb:app_id" content="237755102741371">
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:site_name" content="DNA Distribution Customer Resources">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI'] ?>">

    <?php if ($isImage): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($file['thumbnails']['facebook']); ?>">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:type" content="website">
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
    <style>
        .btn-primary {
          --bs-btn-color: #574057;
          --bs-btn-bg: #d3bfd3;
          --bs-btn-border-color: #574057;
          --bs-btn-hover-color: #fff;
          --bs-btn-hover-bg: #ad9ead;
          --bs-btn-hover-border-color: #574057;
          --bs-btn-focus-shadow-rgb: 49, 132, 253;
          --bs-btn-active-color: #fff;
          --bs-btn-active-bg: #584057;
          --bs-btn-active-border-color: #d4bfd3;
          --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
          --bs-btn-disabled-color: #fff;
          --bs-btn-disabled-bg: #d1c8d1;
          --bs-btn-disabled-border-color: #584057;
        }
        .card {
          --bs-card-spacer-y: 1rem;
          --bs-card-spacer-x: 1rem;
          --bs-card-title-spacer-y: 0.5rem;
          --bs-card-title-color: ;
          --bs-card-subtitle-color: ;
          --bs-card-border-width: var(--bs-border-width);
          --bs-card-border-color: var(--bs-border-color-translucent);
          --bs-card-border-radius: var(--bs-border-radius);
          --bs-card-box-shadow: ;
          --bs-card-inner-border-radius: calc(var(--bs-border-radius) -(var(--bs-border-width)));
          --bs-card-cap-padding-y: 0.5rem;
          --bs-card-cap-padding-x: 1rem;
          --bs-card-cap-bg: rgba(var(--bs-body-color-rgb), 0.03);
          --bs-card-cap-color: ;
          --bs-card-height: ;
          --bs-card-color: ;
          --bs-card-bg: #ac9ead;
          --bs-card-img-overlay-padding: 1rem;
          --bs-card-group-margin: 0.75rem;
          position: relative;
          display: flex
        ;
          flex-direction: column;
          min-width: 0;
          height: var(--bs-card-height);
          color: var(--bs-body-color);
          word-wrap: break-word;
          background-color: var(--bs-card-bg);
          background-clip: border-box;
          border: var(--bs-card-border-width) solid var(--bs-card-border-color);
          border-radius: var(--bs-card-border-radius);
        }
        .bg-dark {
          --bs-bg-opacity: 1;
          background-color: rgb(209 200 209) !important;
        }

        body {
          margin: 0;
          font-family: var(--bs-body-font-family);
          font-size: var(--bs-body-font-size);
          font-weight: var(--bs-body-font-weight);
          line-height: var(--bs-body-line-height);
          color: var(--bs-body-color);
          text-align: var(--bs-body-text-align);
            background-color: rgb(209 200 209) !important;
          -webkit-text-size-adjust: 100%;
          -webkit-tap-highlight-color: transparent;
            color: rgb(88 64 87) !important;
        }.bg-dark {
          --bs-bg-opacity: 1;
          background-color: rgb(209 200 209) !important;
            color: rgb(88 64 87) !important;
        }
        .text-light {
          --bs-text-opacity: 1;
            font-size: 1.33rem;
          color: rgb(88 64 87) !important;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <a href="https://www.dnadistribution.us"><img src="dna_logo_dk.png" alt="DNA Distribution Logo" style="width: 150px;"></a><br/>
                    <h3 class="text-light"><?php echo htmlspecialchars($folderName); ?></h3>
                    <p class="text-muted">DNA Distribution</p>
                </div>
                <div class="card text-center justify-content-center">
                    <div class="card-body text-center justify-content-center">
                        <?php if ($isImage): ?>
                            <img src="<?php echo htmlspecialchars($proxyUrl); ?>" 
                                 alt="<?php echo htmlspecialchars($file['name']); ?>" 
                                 class="img-fluid rounded mb-3">
                        <?php elseif ($isVideo): ?>
                            <video controls autoplay class="w-100 rounded mb-3" src="<?php echo htmlspecialchars($proxyUrl); ?>">
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