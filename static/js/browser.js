document.addEventListener('DOMContentLoaded', function() {
    const filesContainer = document.getElementById('files-container');
    const breadcrumbContainer = document.getElementById('breadcrumb-container');
    const selectAllBtn = document.getElementById('select-all');
    const downloadSelectedBtn = document.getElementById('download-selected');
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

    let selectedFiles = new Set();

    function loadFiles(folderId = null) {
        filesContainer.innerHTML = `
            <div class="col-12">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `;

        // Construct the URL with the folder parameter
        const url = new URL('/list', window.location.origin);
        if (folderId) {
            url.searchParams.append('folder', folderId);
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error loading files');
                }
                renderBreadcrumbs(data.breadcrumbs);
                renderFiles(data.files);
            })
            .catch(error => {
                console.error('Error:', error);
                filesContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            ${error.message}
                        </div>
                    </div>
                `;
            });
    }

    function renderBreadcrumbs(breadcrumbs) {
        if (!breadcrumbs) return;

        const html = `
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    ${breadcrumbs.map((item, index) => `
                        <li class="breadcrumb-item ${index === breadcrumbs.length - 1 ? 'active' : ''}">
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

        // Add click handlers for breadcrumb navigation
        breadcrumbContainer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const folderId = e.target.dataset.folder;
                loadFiles(folderId);
                // Update URL without reloading the page
                history.pushState({}, '', `/?folder=${folderId}`);
            });
        });
    }

    function renderFiles(files) {
        if (!files || files.length === 0) {
            filesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info">No files found in this folder</div>
                </div>
            `;
            return;
        }

        filesContainer.innerHTML = files.map(file => `
            <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            ${file.isFolder ? 
                                '<i class="fas fa-folder fa-3x text-warning"></i>' :
                                '<i class="fas fa-file fa-3x text-info"></i>'
                            }
                        </div>
                        <h5 class="card-title text-truncate" title="${file.name}">
                            ${file.name}
                        </h5>
                        <div class="mt-3">
                            ${file.isFolder ? 
                                `<button class="btn btn-primary btn-sm w-100 folder-link" 
                                         data-folder="${file.id}">
                                    <i class="fas fa-folder-open me-1"></i> Open
                                </button>` :
                                `<a href="${file.webViewLink}" 
                                    target="_blank" 
                                    class="btn btn-info btn-sm w-100">
                                    <i class="fas fa-external-link-alt me-1"></i> View
                                </a>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Add click handlers for folder navigation
        filesContainer.querySelectorAll('.folder-link').forEach(button => {
            button.addEventListener('click', (e) => {
                const folderId = e.target.closest('.folder-link').dataset.folder;
                loadFiles(folderId);
                // Update URL without reloading the page
                history.pushState({}, '', `/?folder=${folderId}`);
            });
        });
    }

    function showPreview(fileId) {
        showDebugToast('Fetching preview...', { fileId });

        fetch(`/preview/${fileId}`)
            .then(response => response.json())
            .then(data => {
                showDebugToast('Preview Response', data);
                document.getElementById('preview-frame').src = data.preview_url;
                previewModal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                showDebugToast('Error loading preview', { error: error.message });
                alert('Error loading preview');
            });
    }

    function updateSelectedFiles() {
        selectedFiles.clear();
        document.querySelectorAll('.file-checkbox:checked').forEach(checkbox => {
            selectedFiles.add(checkbox.value);
        });
        downloadSelectedBtn.disabled = selectedFiles.size === 0;
    }

    selectAllBtn.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('.file-checkbox:not(:disabled)');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        updateSelectedFiles();
    });

    downloadSelectedBtn.addEventListener('click', () => {
        selectedFiles.forEach(fileId => {
            const fileName = document.querySelector(`label[for="check-${fileId}"]`).textContent.trim();
            window.open(`/download?file=${fileId}&name=${encodeURIComponent(fileName)}`);
        });
    });

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