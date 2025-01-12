document.addEventListener('DOMContentLoaded', function() {
    const filesContainer = document.getElementById('files-container');
    const breadcrumbContainer = document.getElementById('breadcrumb-container');
    const selectAllBtn = document.getElementById('select-all');
    const downloadSelectedBtn = document.getElementById('download-selected');
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    
    let selectedFiles = new Set();

    function loadFiles(folderId) {
        filesContainer.innerHTML = `
            <div class="col-12">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `;

        fetch(`/list?folder_id=${folderId}`)
            .then(response => response.json())
            .then(data => {
                renderBreadcrumbs(data.breadcrumbs);
                renderFiles(data.files);
            })
            .catch(error => {
                console.error('Error:', error);
                filesContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">Error loading files</div>
                    </div>
                `;
            });
    }

    function renderBreadcrumbs(breadcrumbs) {
        const html = `
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    ${breadcrumbs.map((item, index) => `
                        <li class="breadcrumb-item ${index === breadcrumbs.length - 1 ? 'active' : ''}">
                            ${index === breadcrumbs.length - 1 ? 
                                item.name :
                                `<a href="#" data-folder-id="${item.id}">${item.name}</a>`
                            }
                        </li>
                    `).join('')}
                </ol>
            </nav>
        `;
        breadcrumbContainer.innerHTML = html;

        // Add click events to breadcrumb links
        breadcrumbContainer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const folderId = e.target.dataset.folderId;
                history.pushState({}, '', `/?folder_id=${folderId}`);
                loadFiles(folderId);
            });
        });
    }

    function renderFiles(files) {
        if (files.length === 0) {
            filesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info">This folder is empty</div>
                </div>
            `;
            return;
        }

        filesContainer.innerHTML = files.map(file => `
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input file-checkbox" type="checkbox" 
                                   value="${file.id}" id="check-${file.id}"
                                   ${file.isFolder ? 'disabled' : ''}>
                            <label class="form-check-label" for="check-${file.id}">
                                ${file.name}
                            </label>
                        </div>
                        <div class="text-center mb-2">
                            ${file.isFolder ?
                                `<i class="fas fa-folder fa-3x text-warning"></i>` :
                                `<img src="${file.thumbnailLink || '/static/img/file.png'}" 
                                      class="img-thumbnail" alt="${file.name}">`
                            }
                        </div>
                        <div class="btn-group w-100">
                            ${file.isFolder ?
                                `<button class="btn btn-secondary btn-sm folder-link" 
                                         data-folder-id="${file.id}">
                                    Open
                                </button>` :
                                `<button class="btn btn-primary btn-sm preview-link" 
                                         data-file-id="${file.id}">
                                    Preview
                                </button>
                                <a href="/download?file_id=${file.id}&file_name=${encodeURIComponent(file.name)}" 
                                   class="btn btn-success btn-sm">
                                    Download
                                </a>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Add click events
        filesContainer.querySelectorAll('.folder-link').forEach(link => {
            link.addEventListener('click', (e) => {
                const folderId = e.target.dataset.folderId;
                history.pushState({}, '', `/?folder_id=${folderId}`);
                loadFiles(folderId);
            });
        });

        filesContainer.querySelectorAll('.preview-link').forEach(link => {
            link.addEventListener('click', (e) => {
                const fileId = e.target.dataset.fileId;
                showPreview(fileId);
            });
        });

        filesContainer.querySelectorAll('.file-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedFiles);
        });
    }

    function showPreview(fileId) {
        fetch(`/preview/${fileId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('preview-frame').src = data.preview_url;
                previewModal.show();
            })
            .catch(error => {
                console.error('Error:', error);
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
            window.open(`/download?file_id=${fileId}&file_name=${encodeURIComponent(fileName)}`);
        });
    });

    // Initial load
    loadFiles(FOLDER_ID);
});
