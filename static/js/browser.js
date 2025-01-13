document.addEventListener('DOMContentLoaded', function() {
    const filesContainer = document.getElementById('files-container');
    const breadcrumbContainer = document.getElementById('breadcrumb-container');

    // Create page transition overlay
    const overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay';
    document.body.appendChild(overlay);

    function showLoadingState() {
        overlay.classList.add('active');
        filesContainer.innerHTML = `
            <div class="loading-spinner">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
    }

    function hideLoadingState() {
        overlay.classList.remove('active');
    }

    function loadFiles(folderId = null) {
        showLoadingState();

        const url = '/list' + (folderId ? `?folder=${encodeURIComponent(folderId)}` : '');

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error loading files');
                }
                renderBreadcrumbs(data.breadcrumbs);
                renderFiles(data.files);
                hideLoadingState();
            })
            .catch(error => {
                console.error('Error:', error);
                filesContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger fade-transition">
                            ${error.message}
                        </div>
                    </div>
                `;
                hideLoadingState();
            });
    }

    function renderBreadcrumbs(breadcrumbs) {
        if (!breadcrumbs) return;

        const html = `
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    ${breadcrumbs.map((item, index) => `
                        <li class="breadcrumb-item ${index === breadcrumbs.length - 1 ? 'active' : ''}"
                            style="--animation-order: ${index};">
                            ${index === breadcrumbs.length - 1 ? 
                                item.name :
                                `<a href="#" data-folder="${item.id}">${item.name}</a>`
                            }
                        </li>
                    `).join('')}
                </ol>
            </nav>
        `;

        breadcrumbContainer.innerHTML = html;

        breadcrumbContainer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const folderId = e.currentTarget.dataset.folder;
                navigateToFolder(folderId);
            });
        });
    }

    function renderFiles(files) {
        if (!files || files.length === 0) {
            filesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info fade-transition show">
                        No files found in this folder
                    </div>
                </div>
            `;
            return;
        }

        filesContainer.innerHTML = files.map((file, index) => `
            <div class="col-sm-6 col-md-4 col-lg-3 mb-3" style="--animation-order: ${index}">
                <div class="card h-100">
                    ${file.isFolder ? 
                        `<div class="card-body">
                            <h5 class="card-title text-truncate" title="${file.name}">
                                <i class="fas fa-folder fa-3x text-warning"></i>
                            </h5>
                            <button type="button" class="btn btn-primary btn-sm w-100 folder-link" 
                                    data-folder-id="${file.id}">
                                <i class="fas fa-folder-open me-1"></i> Open
                            </button>
                        </div>` :
                        `<div class="card-body">
                            ${file.thumbnailLink ? 
                                `<div class="thumbnail-container">
                                    <img src="${file.thumbnailLink}" alt="${file.name}" class="card-img-top">
                                </div>` : 
                                `<i class="fas fa-file fa-3x text-info"></i>`
                            }
                            <h5 class="card-title text-truncate" title="${file.name}">
                                ${file.name}
                            </h5>
                            <a href="${file.webViewLink}" target="_blank" class="btn btn-info btn-sm w-100">
                                <i class="fas fa-external-link-alt me-1"></i> View
                            </a>
                        </div>`
                    }
                </div>
            </div>
        `).join('');

        // Add click handlers for folder navigation
        filesContainer.querySelectorAll('.folder-link').forEach(button => {
            button.addEventListener('click', function(e) {
                const folderId = this.dataset.folderId;
                navigateToFolder(folderId);
            });
        });
    }

    function navigateToFolder(folderId) {
        // Add slide-out animation
        filesContainer.classList.add('folder-transition', 'slide-left');

        setTimeout(() => {
            loadFiles(folderId);
            updateUrl(folderId);

            // Reset container for slide-in animation
            filesContainer.classList.remove('slide-left');
            filesContainer.classList.add('slide-right');

            // Trigger reflow
            void filesContainer.offsetWidth;

            // Remove slide-right class to animate in
            filesContainer.classList.remove('slide-right');
        }, 300);
    }

    function updateUrl(folderId) {
        const newUrl = folderId ? `/?folder=${encodeURIComponent(folderId)}` : '/';
        history.pushState({}, '', newUrl);
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const folderId = urlParams.get('folder');
        loadFiles(folderId);
    });

    // Initial load
    const urlParams = new URLSearchParams(window.location.search);
    const initialFolderId = urlParams.get('folder');
    loadFiles(initialFolderId);
});