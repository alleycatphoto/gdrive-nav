<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drive Browser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@6.4.2/css/all.min.css" rel="stylesheet">
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
                echo '<nav aria-label="breadcrumb" class="mb-3">';
                echo '<ol class="breadcrumb">';
                foreach ($breadcrumbs as $index => $crumb) {
                    if ($index === count($breadcrumbs) - 1) {
                        echo '<li class="breadcrumb-item active">' . htmlspecialchars($crumb['name']) . '</li>';
                    } else {
                        echo '<li class="breadcrumb-item"><a href="/?folder=' . htmlspecialchars($crumb['id']) . '">' . htmlspecialchars($crumb['name']) . '</a></li>';
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
                echo '<div class="row g-3">';
                foreach ($files as $file) {
                    ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <?php if ($file['isFolder']): ?>
                                        <i class="fas fa-folder fa-3x text-warning"></i>
                                    <?php else: ?>
                                        <i class="fas fa-file fa-3x text-info"></i>
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title text-truncate" title="<?php echo htmlspecialchars($file['name']); ?>">
                                    <?php echo htmlspecialchars($file['name']); ?>
                                </h5>
                                <div class="mt-3">
                                    <?php if ($file['isFolder']): ?>
                                        <a href="/?folder=<?php echo htmlspecialchars($file['id']); ?>" 
                                           class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-folder-open me-1"></i> Open
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($file['webViewLink']); ?>" 
                                           target="_blank" 
                                           class="btn btn-info btn-sm w-100">
                                            <i class="fas fa-external-link-alt me-1"></i> View
                                        </a>
                                    <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>