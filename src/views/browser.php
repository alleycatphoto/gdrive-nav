<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drive Browser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --custom-bg: #544055;
            --custom-bg-lighter: #654d66;
            --custom-bg-darker: #443344;
            --custom-primary: #745076;
            --custom-primary-hover: #856087;
            --custom-secondary: #493849;
            --custom-secondary-hover: #5a495a;
            --custom-icon: #b996b9;
        }

        body {
            background-color: var(--custom-bg);
        }

        .navbar {
            background-color: var(--custom-bg-darker) !important;
        }

        .container {
            background-color: var(--custom-bg-lighter);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        /* Breadcrumb styling */
        .breadcrumb {
            background-color: var(--custom-bg-darker);
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .breadcrumb-item {
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--custom-icon);
            opacity: 0.5;
            padding: 0 0.5rem;
            float: none;
            line-height: inherit;
        }

        .breadcrumb-item a,
        .breadcrumb-item.active {
            color: var(--custom-icon);
            text-decoration: none;
            padding: 0.25rem 0.75rem;
            background-color: var(--custom-secondary);
            border-radius: 0.25rem;
            transition: background-color 0.2s;
            display: inline-block;
            line-height: 1.5;
        }

        .breadcrumb-item a:hover {
            background-color: var(--custom-secondary-hover);
            color: var(--custom-icon);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* File cards */
        .card {
            background-color: var(--custom-secondary);
            border: none;
            transition: transform 0.2s, background-color 0.2s;
        }

        .card:hover {
            background-color: var(--custom-secondary-hover);
            transform: translateY(-2px);
        }

        .card-body {
            display: flex;
            flex-direction: column;
            padding: 1.25rem;
        }

        .card-title {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--custom-icon);
        }

        .file-icon {
            font-size: 1.25rem;
            color: var(--custom-icon);
        }

        /* Action buttons styling */
        .action-btn {
            padding: 0.5rem 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--custom-secondary);
            border: 1px solid var(--custom-icon);
            color: var(--custom-icon);
            text-decoration: none;
            border-radius: 0.25rem;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .action-btn:hover {
            background-color: var(--custom-icon);
            color: var(--custom-secondary);
        }

        .folder-link {
            width: 100%;
            padding: 0.5rem 0.75rem;
            background-color: var(--custom-secondary);
            border: 1px solid var(--custom-icon);
            color: var(--custom-icon);
            border-radius: 0.25rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: auto;
        }

        .folder-link:hover {
            background-color: var(--custom-icon);
            color: var(--custom-secondary);
        }

        /* Thumbnail styling */
        .thumbnail-container {
            position: relative;
            padding-bottom: 56.25%;
            background-color: var(--custom-bg-darker);
            border-radius: 0.25rem;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .thumbnail-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-actions {
            margin-top: auto;
            display: flex;
            gap: 0.5rem;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin-top: 1rem;
            }

            .card-title {
                font-size: 0.9rem;
            }

            .action-btn,
            .folder-link {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }
        }

        /* Animation and transition styles */
        .fade-transition {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .fade-transition.show {
            opacity: 1;
        }

        /* Loading animation */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
        }

        .loading-spinner::after {
            content: '';
            width: 50px;
            height: 50px;
            border: 3px solid var(--custom-icon);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Card animations */
        .card {
            opacity: 0;
            transform: translateY(20px);
            animation: cardAppear 0.3s ease-out forwards;
        }

        @keyframes cardAppear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Folder transition animations */
        .folder-transition {
            position: relative;
            transition: all 0.3s ease-in-out;
        }

        .folder-transition.slide-left {
            transform: translateX(-100%);
            opacity: 0;
        }

        .folder-transition.slide-right {
            transform: translateX(100%);
            opacity: 0;
        }

        /* Stagger card animations */
        .col-sm-6 {
            animation-delay: calc(var(--animation-order) * 0.1s);
        }

        /* Page transition overlay */
        .page-transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--custom-bg-darker);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
            z-index: 9999;
        }

        .page-transition-overlay.active {
            opacity: 0.5;
            pointer-events: all;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-folder-open"></i> Drive Browser
            </a>
        </div>
    </nav>

    <div class="container">
        <?php
        // Initialize DriveService
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
                            <?php if (!$file['isFolder'] && $file['thumbnailLink']): ?>
                            <div class="thumbnail-container" 
                                 <?php if ($isPreviewable): ?>
                                 onclick="previewImage('<?php echo htmlspecialchars($file['thumbnailLink']); ?>', '<?php echo htmlspecialchars($file['name']); ?>', '<?php echo htmlspecialchars($file['downloadUrl']); ?>')"
                                 style="cursor: pointer;"
                                 <?php endif; ?>>
                                <img src="<?php echo htmlspecialchars($file['thumbnailLink']); ?>" 
                                     alt="<?php echo htmlspecialchars($file['name']); ?>"
                                     class="card-img-top">
                            </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas <?php echo $fileIcon; ?> file-icon"></i>
                                    <span class="text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </span>
                                </h6>

                                <?php if ($file['isFolder']): ?>
                                    <a href="<?php echo $cardLink; ?>" class="folder-link">
                                        <i class="fas fa-folder-open"></i> Open
                                    </a>
                                <?php else: ?>
                                    <div class="file-actions">
                                        <a href="<?php echo htmlspecialchars($file['webViewLink']); ?>" 
                                           target="_blank"
                                           class="action-btn"
                                           title="View">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="<?php echo htmlspecialchars($file['downloadUrl']); ?>" 
                                           class="action-btn"
                                           title="Download"
                                           download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
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

        <!-- Debug Information Section -->
        <div id="debug-section" class="mt-4">
            <div class="card bg-dark">
                <div class="card-header">
                    Debug Information
                </div>
                <div class="card-body">
                    <pre id="debug-output" class="mb-0 text-light">
<?php
    echo json_encode([
        'current_folder' => $currentFolderId,
        'is_shared_drive' => $_ENV['GOOGLE_DRIVE_IS_SHARED'],
        'drive_id' => $_ENV['GOOGLE_DRIVE_ROOT_FOLDER'],
        'request_uri' => $_SERVER['REQUEST_URI'],
        'get_params' => $_GET,
    ], JSON_PRETTY_PRINT);
?>
                    </pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="previewModalLabel">Image Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="previewImage" src="" alt="" class="img-fluid" style="max-height: 80vh; max-width: 90vw; width: auto; height: auto; object-fit: contain;">
                </div>
                <div class="modal-footer border-secondary">
                    <a id="modalDownloadLink" href="#" class="btn btn-primary" download>
                        <i class="fas fa-download me-2"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize preview modal
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

        function previewImage(src, title, downloadUrl) {
            const modalTitle = document.getElementById('previewModalLabel');
            const modalImage = document.getElementById('previewImage');
            const downloadLink = document.getElementById('modalDownloadLink');

            modalTitle.textContent = title;
            // Use high-res thumbnail for preview
            const highResThumbnail = src.replace(/=s\d+$/, '=s1024');
            modalImage.src = highResThumbnail;
            downloadLink.href = downloadUrl;

            previewModal.show();
        }
    </script>
</body>
</html>