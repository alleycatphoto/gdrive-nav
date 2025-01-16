document.addEventListener('DOMContentLoaded', function() {
    const filesContainer = document.getElementById('files-container');
    const breadcrumbContainer = document.getElementById('breadcrumb-container');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');

    // Create page transition overlay
    const overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay';
    document.body.appendChild(overlay);

    function showLoadingState(message = 'Loading...') {
        overlay.classList.add('active');
        filesContainer.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="search-progress">
                    <p class="mb-2">${message}</p>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        `;
    }

    function hideLoadingState() {
        overlay.classList.remove('active');
    }

    function updateSearchProgress(message) {
        const progressElement = filesContainer.querySelector('.search-progress p');
        if (progressElement) {
            progressElement.textContent = message;
        }
    }

    function loadFiles(folderId = null, searchQuery = null) {
        let startTime;
        let searchTimeout;
        let longSearchMessage;

        if (searchQuery) {
            showLoadingState('Searching files...');
            startTime = Date.now();

            // Show additional messages if search takes longer
            searchTimeout = setTimeout(() => {
                updateSearchProgress('Searching through folders... This might take a minute...');
                longSearchMessage = setInterval(() => {
                    const seconds = Math.floor((Date.now() - startTime) / 1000);
                    updateSearchProgress(`Still searching... (${seconds} seconds)`);
                }, 1000);
            }, 3000);
        } else {
            showLoadingState();
        }

        let url = '/list';
        const params = new URLSearchParams();

        if (folderId) {
            params.append('folder', folderId);
        }
        if (searchQuery) {
            params.append('search', searchQuery);
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        console.log('Fetching URL:', url); // Debug log

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error loading files');
                }
                console.log('Received data:', data); // Debug log
                renderBreadcrumbs(data.breadcrumbs);
                renderFiles(data.files);
                hideLoadingState();
            })
            .catch(error => {
                console.error('Error:', error);
                filesContainer.innerHTML = `
                    <div class="alert alert-danger fade-transition">
                        ${error.message}
                    </div>
                `;
                hideLoadingState();
            })
            .finally(() => {
                // Clear all timeouts and intervals
                if (searchTimeout) clearTimeout(searchTimeout);
                if (longSearchMessage) clearInterval(longSearchMessage);
            });
    }

    // Handle search form submission
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchQuery = searchInput.value.trim();
            if (!searchQuery) return;

            const urlParams = new URLSearchParams(window.location.search);
            const currentFolder = urlParams.get('folder');

            console.log('Search submitted:', { searchQuery, currentFolder }); // Debug log
            loadFiles(currentFolder, searchQuery);
            updateUrl(currentFolder, searchQuery);
        });
    }

    function updateUrl(folderId, searchQuery) {
        const params = new URLSearchParams();
        if (folderId) params.set('folder', folderId);
        if (searchQuery) params.set('search', searchQuery);

        const newUrl = params.toString() ? `/?${params.toString()}` : '/';
        history.pushState({}, '', newUrl);
    }

    function renderFiles(files) {
        if (!files || files.length === 0) {
            filesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info fade-transition">
                        No files found
                    </div>
                </div>
            `;
            return;
        }

        const html = files.map((file, index) => `
            <div class="col-sm-6 col-md-4 col-lg-3" style="--animation-order: ${index}">
                <div class="card h-100">
                    ${file.isFolder ? 
                        `<div class="card-body">
                            <h6 class="card-title">
                                <a href="/?folder=${encodeURIComponent(file.id)}"
                                   class="d-flex align-items-center gap-2 text-decoration-none text-truncate"
                                   style="color: inherit;">
                                    <i class="fas fa-folder file-icon"></i>
                                    <span class="text-truncate" title="${file.name}">
                                        ${file.name}
                                    </span>
                                </a>
                            </h6>
                            <a href="/?folder=${encodeURIComponent(file.id)}" class="folder-link">
                                <i class="fas fa-folder-open"></i> Open
                            </a>
                        </div>` :
                        `<div class="card-body">
                            ${file.thumbnailLink ? 
                                `<div class="thumbnail-container">
                                    <img src="${file.thumbnailLink}" alt="${file.name}" class="card-img-top">
                                </div>` : 
                                `<div class="text-center py-3">
                                    <i class="fas fa-file fa-3x"></i>
                                </div>`
                            }
                            <h6 class="card-title text-truncate" title="${file.name}">
                                ${file.name}
                            </h6>
                            <div class="file-actions">
                                <a href="${file.webViewLink}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="${file.downloadUrl}" class="btn btn-sm btn-secondary" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>`
                    }
                </div>
            </div>
        `).join('');

        filesContainer.innerHTML = `<div class="row g-4">${html}</div>`;
    }

    function renderBreadcrumbs(breadcrumbs) {
        if (!breadcrumbs || !breadcrumbContainer) return;

        const html = `
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    ${breadcrumbs.map((item, index) => `
                        <li class="breadcrumb-item ${index === breadcrumbs.length - 1 ? 'active' : ''}"
                            style="--animation-order: ${index};">
                            ${index === breadcrumbs.length - 1 ? 
                                item.name :
                                `<a href="/?folder=${encodeURIComponent(item.id)}">${item.name}</a>`
                            }
                        </li>
                    `).join('')}
                </ol>
            </nav>
        `;

        breadcrumbContainer.innerHTML = html;
    }


    // Handle browser back/forward buttons
    window.addEventListener('popstate', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const folderId = urlParams.get('folder');
        const searchQuery = urlParams.get('search');
        loadFiles(folderId, searchQuery);
    });

    // Initial load
    const urlParams = new URLSearchParams(window.location.search);
    const initialFolderId = urlParams.get('folder');
    const initialSearchQuery = urlParams.get('search');
    loadFiles(initialFolderId, initialSearchQuery);
});