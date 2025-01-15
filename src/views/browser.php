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
        <div class="row mb-4">
            <div class="col-12">
                <form class="d-flex" id="search-form">
                    <input class="form-control me-2" type="search" id="search-input" 
                           placeholder="Search files..." aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <?php
        // Initialize DriveService and display files
        try {
            $driveService = new \App\Services\DriveService();
            $currentFolderId = isset($_GET['folder']) ? $_GET['folder'] : null;
            $searchQuery = isset($_GET['search']) ? $_GET['search'] : null;
            $breadcrumbs = $driveService->getBreadcrumbs($currentFolderId);

            // Display breadcrumbs
            if (!empty($breadcrumbs)) {
                echo '<nav aria-label="breadcrumb" class="mb-4" id="breadcrumb-container">';
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

            // Create container for files
            echo '<div id="files-container">';

            // List files in current folder or search results
            $files = $searchQuery ? 
                $driveService->searchFiles($searchQuery, $currentFolderId) : 
                $driveService->listFiles($currentFolderId);

            if (empty($files)) {
                echo '<div class="alert alert-info">No files found</div>';
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
                    ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100">
                            <?php if (!$file['isFolder']): ?>
                                <?php if (isset($file['thumbnailLink'])): ?>
                                    <div class="thumbnail-container">
                                        <img src="<?php echo htmlspecialchars($file['thumbnailLink']); ?>"
                                             alt="<?php echo htmlspecialchars($file['name']); ?>"
                                             class="card-img-top">
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="card-body">
                                <h6 class="card-title">
                                    <?php if ($file['isFolder']): ?>
                                        <a href="<?php echo $cardLink; ?>" class="d-flex align-items-center gap-2 text-decoration-none text-truncate">
                                            <i class="fas <?php echo $fileIcon; ?> file-icon"></i>
                                            <span class="text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                                <?php echo htmlspecialchars($file['name']); ?>
                                            </span>
                                        </a>
                                    <?php else: ?>
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="fas <?php echo $fileIcon; ?> file-icon"></i>
                                            <span class="text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                                <?php echo htmlspecialchars($file['name']); ?>
                                            </span>
                                        </span>
                                    <?php endif; ?>
                                </h6>

                                <?php if (!$file['isFolder']): ?>
                                    <div class="file-actions">
                                        <a href="<?php echo htmlspecialchars($file['webViewLink']); ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="<?php echo htmlspecialchars($file['downloadUrl']); ?>"
                                           class="btn btn-sm btn-secondary"
                                           download>
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <a href="<?php echo $cardLink; ?>" class="folder-link">
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
            echo '</div>'; // Close files-container
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>

    <?php include __DIR__ . '/../includes/scripts.php'; ?>
    <script src="/static/js/browser.js"></script>
</body>
</html>