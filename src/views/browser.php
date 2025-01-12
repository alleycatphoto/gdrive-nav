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
        <div id="breadcrumb-container" class="mb-3"></div>
        <div id="files-container" class="row g-3">
            <div class="col-12">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debug Information Section -->
        <div id="debug-section" class="mt-4">
            <div class="card bg-dark">
                <div class="card-header">
                    Debug Information
                </div>
                <div class="card-body">
                    <pre id="debug-output" class="mb-0 text-light"></pre>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fetch files from the API
        fetch('/list')
            .then(response => response.json())
            .then(data => {
                document.getElementById('debug-output').textContent = JSON.stringify(data, null, 2);

                if (!data.success) {
                    document.getElementById('files-container').innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger">
                                ${data.error || 'Error loading files'}
                            </div>
                        </div>
                    `;
                    return;
                }

                // Handle files display here
                const filesContainer = document.getElementById('files-container');
                if (!data.files || data.files.length === 0) {
                    filesContainer.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info">No files found</div>
                        </div>
                    `;
                    return;
                }

                filesContainer.innerHTML = data.files.map(file => `
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    ${file.isFolder ? 
                                        '<i class="fas fa-folder text-warning"></i>' : 
                                        '<i class="fas fa-file text-info"></i>'} 
                                    ${file.name}
                                </h5>
                                <p class="card-text small text-muted">${file.mimeType}</p>
                            </div>
                            <div class="card-footer">
                                ${file.isFolder ? 
                                    `<a href="/?folder=${file.id}" class="btn btn-primary btn-sm">Open</a>` :
                                    `<a href="${file.webViewLink}" target="_blank" class="btn btn-primary btn-sm">View</a>`}
                            </div>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => {
                document.getElementById('debug-output').textContent = JSON.stringify({error: error.message}, null, 2);
                document.getElementById('files-container').innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            Error: ${error.message}
                        </div>
                    </div>
                `;
            });
    </script>
</body>
</html>