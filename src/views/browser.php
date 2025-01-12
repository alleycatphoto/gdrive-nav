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
        }

        .breadcrumb-item {
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--custom-icon);
            opacity: 0.5;
        }

        .breadcrumb-item a {
            color: var(--custom-icon);
            text-decoration: none;
            padding: 0.25rem 0.5rem;
            background-color: var(--custom-bg-darker);
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }

        .breadcrumb-item a:hover {
            background-color: var(--custom-bg);
            color: var(--custom-icon);
        }

        .breadcrumb-item.active {
            color: var(--custom-icon);
            padding: 0.25rem 0.5rem;
            background-color: var(--custom-bg-darker);
            border-radius: 0.25rem;
            margin: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* File cards */
        .file-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .file-icon {
            color: var(--custom-icon);
            font-size: 1em;
            margin-right: 0.5em;
            vertical-align: middle;
        }

        .thumbnail-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            background-color: var(--custom-bg-darker);
            border-radius: 0.25rem;
            overflow: hidden;
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
            position: absolute;
            right: 0.5rem;
            bottom: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s;
            display: flex;
            gap: 0.5rem;
        }

        .card:hover .file-actions {
            opacity: 1;
        }

        .action-btn {
            padding: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--custom-bg-darker);
            border: none;
            color: var(--custom-icon);
            text-decoration: none;
        }

        .action-btn:hover {
            background-color: var(--custom-bg);
            color: var(--custom-icon);
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin-top: 1rem;
            }

            .file-actions {
                opacity: 1;
            }
            .breadcrumb {
                padding: 0.5rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }
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
                        <div class="card h-100 file-card" 
                             <?php if ($file['isFolder']): ?>
                             onclick="window.location='<?php echo $cardLink; ?>'"
                             <?php elseif ($isPreviewable): ?>
                             onclick="previewImage('<?php echo htmlspecialchars($file['webViewLink']); ?>', '<?php echo htmlspecialchars($file['name']); ?>', '<?php echo htmlspecialchars($file['downloadUrl']); ?>')"
                             <?php endif; ?>>
                            <?php if (!$file['isFolder'] && $file['thumbnailLink']): ?>
                            <div class="thumbnail-container">
                                <img src="<?php echo htmlspecialchars($file['thumbnailLink']); ?>" 
                                     alt="<?php echo htmlspecialchars($file['name']); ?>"
                                     class="card-img-top">
                            </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <h6 class="card-title text-truncate mb-3" title="<?php echo htmlspecialchars($file['name']); ?>">
                                    <i class="fas <?php echo $fileIcon; ?> file-icon"></i>
                                    <?php echo htmlspecialchars($file['name']); ?>
                                </h6>

                                <?php if (!$file['isFolder']): ?>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="previewImage" src="" alt="" class="img-fluid">
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
            modalImage.src = src;
            downloadLink.href = downloadUrl;
            previewModal.show();
        }
    </script>
</body>
</html>