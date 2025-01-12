<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drive Browser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.4.2/dist/photoswipe.css">
    <style>
        :root {
            --custom-bg: #544055;
            --custom-bg-lighter: #654d66;
            --custom-bg-darker: #443344;
            --custom-primary: #745076;
            --custom-primary-hover: #856087;
            --custom-secondary: #493849;
            --custom-secondary-hover: #5a495a;
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

        .btn-primary {
            background-color: var(--custom-primary);
            border-color: var(--custom-primary);
        }

        .btn-primary:hover {
            background-color: var(--custom-primary-hover);
            border-color: var(--custom-primary-hover);
        }

        /* Breadcrumb animations */
        .breadcrumb-item {
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* File cards */
        .file-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .file-icon {
            font-size: 0.9em;
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
        }

        .card:hover .file-actions {
            opacity: 1;
        }

        .dropdown-menu {
            background-color: var(--custom-bg-darker);
            border-color: var(--custom-primary);
        }

        .dropdown-item {
            color: #fff;
        }

        .dropdown-item:hover {
            background-color: var(--custom-primary);
            color: #fff;
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

            // Get current folder ID from query parameters
            $currentFolderId = isset($_GET['folder']) ? $_GET['folder'] : null;

            // Get breadcrumbs for current folder
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
                    $fileColorClass = $file['isFolder'] ? 'text-warning' : 'text-info';

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
                    ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 file-card">
                            <?php if (!$file['isFolder'] && $file['thumbnailLink']): ?>
                            <div class="thumbnail-container">
                                <img src="<?php echo htmlspecialchars($file['thumbnailLink']); ?>" 
                                     alt="<?php echo htmlspecialchars($file['name']); ?>"
                                     class="card-img-top"
                                     <?php if (strpos($file['mimeType'], 'image/') === 0): ?>
                                     data-pswp-src="<?php echo htmlspecialchars($file['webViewLink']); ?>"
                                     <?php endif; ?>>
                            </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <h6 class="card-title text-truncate mb-3" title="<?php echo htmlspecialchars($file['name']); ?>">
                                    <i class="fas <?php echo $fileIcon; ?> file-icon <?php echo $fileColorClass; ?>"></i>
                                    <?php echo htmlspecialchars($file['name']); ?>
                                </h6>

                                <div class="btn-group file-actions">
                                    <button type="button" class="btn btn-dark btn-sm dropdown-toggle" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php if ($file['isFolder']): ?>
                                        <li>
                                            <a class="dropdown-item" href="/?folder=<?php echo htmlspecialchars($file['id']); ?>">
                                                <i class="fas fa-folder-open me-2"></i> Open
                                            </a>
                                        </li>
                                        <?php else: ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo htmlspecialchars($file['webViewLink']); ?>" 
                                               target="_blank">
                                                <i class="fas fa-external-link-alt me-2"></i> View
                                            </a>
                                        </li>
                                        <?php if (strpos($file['mimeType'], 'image/') === 0): ?>
                                        <li>
                                            <a class="dropdown-item preview-link" href="#" 
                                               data-pswp-src="<?php echo htmlspecialchars($file['webViewLink']); ?>"
                                               data-pswp-title="<?php echo htmlspecialchars($file['name']); ?>">
                                                <i class="fas fa-search-plus me-2"></i> Preview
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
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

    <!-- PhotoSwipe template -->
    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">
            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>
            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div>
                    <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                    <button class="pswp__button pswp__button--share" title="Share"></button>
                    <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                    <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                            <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                </div>
                <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.2/dist/photoswipe.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.2/dist/photoswipe-lightbox.umd.min.js"></script>
    <script>
        // Initialize PhotoSwipe
        const lightbox = new PhotoSwipeLightbox({
            gallery: '.row',
            children: 'a[data-pswp-src]',
            pswpModule: PhotoSwipe
        });
        lightbox.init();

        // Add click handler for preview links
        document.querySelectorAll('.preview-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const options = {
                    dataSource: [{
                        src: link.dataset.pswpSrc,
                        w: 1024,
                        h: 768,
                        title: link.dataset.pswpTitle
                    }],
                    index: 0
                };
                new PhotoSwipe(options).init();
            });
        });
    </script>
</body>
</html>