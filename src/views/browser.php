<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DNA Distribution : Customer Resources</title>
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
            --custom-icon-hover: #d2b9d2; /* Added hover color */
        }

        body {
            background-color: var(--custom-bg);
        }

        .navbar {
            background-color: var(--custom-bg-darker) !important;
        }

        .navbar-brand img {
            height: 30px;
            width: auto;
            margin-right: 10px;
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
            gap: 0.5rem;
            color: var(--custom-icon);
            cursor: pointer;
            transition: color 0.2s;
            width: 100%;
            padding: 1rem;
        }

        .card-title a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            text-decoration: none;
            color: inherit;
        }

        .card-title .text-truncate {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-title:hover {
            color: var(--custom-icon-hover);
        }

        .file-icon {
            font-size: 1rem;
            color: inherit;
            flex-shrink: 0;
        }

        .card-title span {
            flex: 1;
            min-width: 0;
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

        .thumbnail-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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

        /* Modal styles - Edited styles */
        .modal-content {
            max-width: 90vw;
            margin: 0 auto;
            background-color: var(--custom-bg-darker) !important;
            border: 1px solid var(--custom-primary);
        }

        .modal-header {
            border-bottom-color: var(--custom-primary);
            background-color: var(--custom-bg);
        }

        .modal-body {
            background-color: #000;
            padding: 0;
            max-height: calc(90vh - 120px); /* Account for header and footer */
            overflow: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-body video {
            max-width: 100%;
            max-height: 80vh;
            width: auto;
            height: auto;
        }

        .modal-body img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .modal-footer {
            border-top-color: var(--custom-primary);
            background-color: var(--custom-bg);
        }

        .modal .btn-primary {
            background-color: var(--custom-primary);
            border-color: var(--custom-primary);
        }

        .modal .btn-primary:hover {
            background-color: var(--custom-primary-hover);
            border-color: var(--custom-primary-hover);
        }

        .modal .btn-secondary {
            background-color: var(--custom-secondary);
            border-color: var(--custom-secondary);
        }

        .modal .btn-secondary:hover {
            background-color: var(--custom-secondary-hover);
            border-color: var(--custom-secondary-hover);
        }

        /* Update modal dialog size */
        .modal-dialog {
            max-width: 90vw;
            max-height: 90vh;
            margin: 0.5rem auto;
        }

        /* PDF preview container */
        .pdf-container {
            width: 100%;
            height: calc(90vh - 120px);
            background: #000;
        }

        /* Adjust object tag for PDFs */
        .modal-body object {
            width: 100%;
            height: 100%;
            display: block;
        }

        .navbar-brand {
            font-size: 0.8rem;
        }
        .btn-close-white {
            filter: invert(1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid" style="justify-content: center;">
            <a class="navbar-brand" href="/">
                <img src="/attached_assets/Cryoskin White Transparent.png" alt="DNA Distribution Logo">
                DNA DISTRIBUTION : CUSTOMER RESOURCES
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
                            <?php if (!$file['isFolder']): ?>
                                <div class="card-body">
                                    <?php if (strpos($file['mimeType'], 'video/') === 0): ?>
                                        <div class="thumbnail-container">
                                            <video controls class="w-100 h-100" style="position: absolute; top: 0; left: 0;">
                                                <source src="/proxy/<?php echo htmlspecialchars($file['id']); ?>" type="<?php echo htmlspecialchars($file['mimeType']); ?>">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    <?php elseif ($file['thumbnailLink']): ?>
                                        <div class="thumbnail-container" 
                                             onclick="previewFile('<?php echo htmlspecialchars($file['highResThumbnail'] ?? $file['thumbnailLink']); ?>', '<?php echo htmlspecialchars($file['name']); ?>', '<?php echo htmlspecialchars($file['downloadUrl']); ?>', '<?php echo htmlspecialchars($file['mimeType']); ?>', '<?php echo htmlspecialchars($file['webViewLink']); ?>')"
                                             style="cursor: pointer;">
                                            <img src="<?php echo htmlspecialchars($file['thumbnailLink']); ?>" 
                                                 alt="<?php echo htmlspecialchars($file['name']); ?>"
                                                 class="card-img-top">
                                        </div>
                                    <?php endif; ?>

                                    <h6 class="card-title" 
                                        onclick="previewFile('<?php echo htmlspecialchars($file['highResThumbnail'] ?? $file['thumbnailLink']); ?>', '<?php echo htmlspecialchars($file['name']); ?>', '<?php echo htmlspecialchars($file['downloadUrl']); ?>', '<?php echo htmlspecialchars($file['mimeType']); ?>', '<?php echo htmlspecialchars($file['webViewLink']); ?>')"
                                        style="cursor: pointer;">
                                        <i class="fas <?php echo $fileIcon; ?> file-icon"></i>
                                        <span class="text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                            <?php echo htmlspecialchars($file['name']); ?>
                                        </span>
                                    </h6>

                                    <div class="file-actions">
                                        <button onclick="previewFile('<?php echo htmlspecialchars($file['highResThumbnail'] ?? $file['thumbnailLink']); ?>', '<?php echo htmlspecialchars($file['name']); ?>', '<?php echo htmlspecialchars($file['downloadUrl']); ?>', '<?php echo htmlspecialchars($file['mimeType']); ?>', '<?php echo htmlspecialchars($file['webViewLink']); ?>')"
                                                class="action-btn"
                                                title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?php echo htmlspecialchars($file['downloadUrl']); ?>" 
                                           class="action-btn"
                                           title="Download"
                                           download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="/?folder=<?php echo htmlspecialchars($file['id']); ?>" 
                                           class="d-flex align-items-center">
                                            <i class="fas <?php echo $fileIcon; ?> file-icon me-2"></i>
                                            <span class="text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                                <?php echo htmlspecialchars($file['name']); ?>
                                            </span>
                                        </a>
                                    </h6>
                                    <a href="/?folder=<?php echo htmlspecialchars($file['id']); ?>" class="folder-link mt-2">
                                        <i class="fas fa-folder-open"></i> Open
                                    </a>
                                </div>
                            <?php endif; ?>
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

        <?php if (!filter_var($_ENV['PRODUCTION'] ?? 'false', FILTER_VALIDATE_BOOLEAN)): ?>
        <!-- Debug Information Section (Only shown in non-production) -->
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
                    <video id="previewVideo" controls style="max-height: 80vh; max-width: 100%; display: none;">
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
                    <div id="previewFallback" class="p-4" style="display: none;">
                        <p>This file type cannot be previewed directly.</p>
                        <a id="fallbackLink" href="#" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i> Open in Google Drive
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize preview modal
            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            const previewImage = document.getElementById('previewImage');
            const previewVideo = document.getElementById('previewVideo');
            const pdfContainer = document.getElementById('pdfContainer');
            const previewPdf = document.getElementById('previewPdf');
            const pdfDownloadLink = document.getElementById('pdfDownloadLink');
            const previewFallback = document.getElementById('previewFallback');
            const fallbackLink = document.getElementById('fallbackLink');

            // Function to get proxy URL for a file
            function getProxyUrl(fileId) {
                return `/proxy/${fileId}`;
            }

            // Function to extract file ID from Google Drive URL
            function extractFileId(url) {
                const match = url.match(/[-\w]{25,}/);
                return match ? match[0] : null;
            }

            // Function to preview file
            window.previewFile = function(src, title, downloadUrl, mimeType, webViewLink) {
                const modalTitle = document.getElementById('previewModalLabel');
                const downloadLink = document.getElementById('modalDownloadLink');
                const fileId = extractFileId(downloadUrl);
                const proxyUrl = fileId ? getProxyUrl(fileId) : downloadUrl;

                modalTitle.textContent = title;
                downloadLink.href = downloadUrl;

                // Reset all preview elements
                previewImage.style.display = 'none';
                previewVideo.style.display = 'none';
                pdfContainer.style.display = 'none';
                previewFallback.style.display = 'none';

                // Handle different file types
                if (mimeType.startsWith('image/')) {
                    previewImage.src = src;
                    previewImage.style.display = 'block';
                } else if (mimeType.startsWith('video/')) {
                    const videoSource = previewVideo.querySelector('source');
                    videoSource.src = proxyUrl;
                    videoSource.type = mimeType;
                    previewVideo.load(); // Reload the video with new source
                    previewVideo.style.display = 'block';
                } else if (mimeType === 'application/pdf') {
                    previewPdf.data = proxyUrl;
                    pdfDownloadLink.href = downloadUrl;
                    pdfContainer.style.display = 'block';
                } else {
                    previewFallback.style.display = 'block';
                    fallbackLink.href = webViewLink;
                }

                previewModal.show();
            }
        });
    </script>
</body>
</html>