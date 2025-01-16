<?php
// Get current user
$authService = new \App\Services\AuthService();
$currentUser = $authService->getCurrentUser();

// Include the header
include __DIR__ . '/../includes/header.php';
?>
<body>
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="container">
        <?php
        // Initialize DriveService and display files
        try {
            $driveService = new \App\Services\DriveService();
            $currentFolderId = isset($_GET['folder']) ? $_GET['folder'] : null;
            $breadcrumbs = $driveService->getBreadcrumbs($currentFolderId);

            // Display breadcrumbs
            if (!empty($breadcrumbs)) {
                echo '<nav aria-label="breadcrumb" class="mb-4">';
                echo '<ol class="breadcrumb">';
                foreach ($breadcrumbs as $index => $crumb) {
                    $delay = $index * 0.1;
                    if ($index === count($breadcrumbs) - 1) {
                        echo '<li class="breadcrumb-item active" style="animation-delay: ' . $delay . 's">'
                            . htmlspecialchars($crumb['name']) . '</li>';
                    } else {
                        echo '<li class="breadcrumb-item" style="animation-delay: ' . $delay . 's">'
                            . '<a href="/?folder=' . htmlspecialchars($crumb['id']) . '">'
                            . htmlspecialchars($crumb['name']) . '</a></li>';
                    }
                }
                echo '</ol>';
                echo '</nav>';
            }

            // List files in current folder
            $files = $driveService->listFiles($currentFolderId);

            if (empty($files)) {
                echo '<div class="alert alert-info">No files found in this folder</div>';
            } else {
                echo '<div class="row g-4">';
                foreach ($files as $file) {
                    $fileIcon = $file['isFolder'] ? 'fa-folder' : 'fa-file';

                    // Determine file type icon
                    if (!$file['isFolder']) {
                        if (strpos($file['mimeType'], 'image/') === 0) {
                            $fileIcon = 'fa-image';
                        } elseif (strpos($file['mimeType'], 'video/') === 0) {
                            $fileIcon = 'fa-video';
                        } elseif (strpos($file['mimeType'], 'audio/') === 0) {
                            $fileIcon = 'fa-music';
                        } elseif (strpos($file['mimeType'], 'text/') === 0) {
                            $fileIcon = 'fa-file-alt';
                        } elseif (strpos($file['mimeType'], 'application/pdf') === 0) {
                            $fileIcon = 'fa-file-pdf';
                        }
                    }

                    $cardLink = $file['isFolder'] ? '/?folder=' . htmlspecialchars($file['id']) : 'javascript:void(0);';
                    $isPreviewable = !$file['isFolder'] && (strpos($file['mimeType'], 'image/') === 0);
                    ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100">
                            <?php
                            if (!$file['isFolder']) {
                                $thumbnailLink = $file['thumbnailLink'] ?? null;
                                $highResThumbnail = $file['highResThumbnail'] ?? null;
                                $isVideo = strpos($file['mimeType'], 'video/') === 0;
                                $previewProps = json_encode([
                                    'thumbnail' => $highResThumbnail ?? $thumbnailLink,
                                    'name' => $file['name'],
                                    'downloadUrl' => $file['downloadUrl'],
                                    'mimeType' => $file['mimeType'],
                                    'webViewLink' => $file['webViewLink']
                                ], JSON_THROW_ON_ERROR);

                                if ($thumbnailLink) {
                                    echo '<div class="thumbnail-container ' . ($isVideo ? 'video-thumbnail' : '') . '"';
                                    echo ' onclick="previewFile(' . htmlspecialchars($previewProps, ENT_QUOTES) . ')"';
                                    echo ' style="cursor: pointer;">';
                                    echo '<img src="' . htmlspecialchars($thumbnailLink) . '"';
                                    echo ' alt="' . htmlspecialchars($file['name']) . '"';
                                    echo ' class="card-img-top">';

                                    if ($isVideo) {
                                        echo '<div class="video-play-overlay">';
                                        echo '<i class="fas fa-play"></i>';
                                        echo '</div>';
                                    }

                                    echo '</div>';
                                }
                            }
                            ?>

                            <div class="card-body">
                                <?php if (!$file['isFolder']): ?>
                                    <h6 class="card-title"
                                        onclick="previewFile(<?php echo htmlspecialchars($previewProps, ENT_QUOTES); ?>)"
                                        style="cursor: pointer;">
                                        <i class="fas <?php echo $fileIcon; ?> file-icon"></i>
                                        <span class="text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                            <?php echo htmlspecialchars($file['name']); ?>
                                        </span>
                                    </h6>

                                    <div class="file-actions">
                                        <button onclick="previewFile(<?php echo htmlspecialchars($previewProps, ENT_QUOTES); ?>)"
                                                class="action-btn"
                                                title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php
                                        // Add share button
                                        $fileId = extractFileId($file['downloadUrl']);
                                        if ($fileId): ?>
                                            <button class="action-btn"
                                                    data-file-id="<?php echo htmlspecialchars($fileId); ?>"
                                                    onclick="copyShareLink('<?php echo htmlspecialchars($fileId); ?>')"
                                                    title="Share">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo htmlspecialchars($file['downloadUrl']); ?>"
                                           class="action-btn"
                                           title="Download"
                                           download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <h6 class="card-title">
                                        <a href="/?folder=<?php echo htmlspecialchars($file['id']); ?>"
                                           class="d-flex align-items-center gap-2 text-decoration-none text-truncate"
                                           style="color: inherit; width: 100%;">
                                            <i class="fas <?php echo $fileIcon; ?> file-icon"></i>
                                            <span class="text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                                <?php echo htmlspecialchars($file['name']); ?>
                                            </span>
                                        </a>
                                    </h6>
                                    <a href="/?folder=<?php echo htmlspecialchars($file['id']); ?>" class="folder-link">
                                        <i class="fas fa-folder-open"></i> Open
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>

        <div class="container mt-5">
            <div class="collection-header">
                <h4>Featured Products</h4>
            </div>
            <div id='collection-component-1736831470697'>
                <!-- Shopify buy button will be injected here -->
            </div>
        </div>

        <?php if (!filter_var($_ENV['PRODUCTION'] ?? 'false', FILTER_VALIDATE_BOOLEAN)): ?>
        <!-- Debug Information Section (Only shown in non-production) -->
        <div id="debug-section" class="mt-4">
            <div class="card bg-dark">
                <div class="card-header">Debug Information</div>
                <div class="card-body">
                    <pre id="debug-output" class="mb-0 text-light">
                    <?php
                        echo json_encode([
                            'current_folder' => $currentFolderId,
                            'is_shared_drive' => $_ENV['GOOGLE_DRIVE_IS_SHARED'] ?? false,
                            'drive_id' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'],
                            'request_uri' => $_SERVER['REQUEST_URI'],
                            'get_params' => $_GET,
                            'production_mode' => $_ENV['PRODUCTION'] ?? false,
                        ], JSON_PRETTY_PRINT);
                    ?>
                    </pre>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Image preview -->
                    <img id="previewImage" src="" alt="" class="img-fluid" style="max-height: 80vh; max-width: 100%; width: auto; height: auto; object-fit: contain; display: none;">
                    <!-- Video preview -->
                    <video id="previewVideo" controls autoplay style="max-height: 80vh; max-width: 100%; display: none;">
                        <source src="" type="">
                        Your browser does not support the video player.
                    </video>
                    <!-- PDF preview -->
                    <div id="pdfContainer" class="pdf-container" style="display: none;">
                        <object id="previewPdf" data="" type="application/pdf">
                            <p>Unable to display PDF file. <a id="pdfDownloadLink" href="#" target="_blank">Download</a> instead.</p>
                        </object>
                    </div>
                    <!-- Fallback message -->
                    <div id="previewFallback" style="display: none;">
                        <p>This file type cannot be previewed directly.</p>
                        <a id="fallbackLink" href="#" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i> Open in Google Drive
                        </a>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" id="modalShareBtn" class="btn btn-primary" data-file-id="">
                        <i class="fas fa-share-alt me-2"></i> Share
                    </button>
                    <a id="modalDownloadLink" href="#" class="btn btn-primary" download>
                        <i class="fas fa-download me-2"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Helper function to extract file ID from Google Drive URL
    function extractFileId($url) {
        if (!$url) return null;
        if (preg_match('/[-\w]{25,}/', $url, $matches)) {
            return $matches[0];
        }
        return null;
    }
    ?>

    <?php include __DIR__ . '/../includes/scripts.php'; ?>
</body>
</html>