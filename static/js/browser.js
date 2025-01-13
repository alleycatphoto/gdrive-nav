document.addEventListener('DOMContentLoaded', function() {
    const filesContainer = document.getElementById('files-container');
    const breadcrumbContainer = document.getElementById('breadcrumb-container');
    const userSection = document.getElementById('userSection');
    const loginBtn = document.getElementById('loginBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    const userName = document.getElementById('userName');
    const userAvatar = document.getElementById('userAvatar');

    // Create page transition overlay
    const overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay';
    document.body.appendChild(overlay);

    // Initialize preview modal
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    const previewImage = document.getElementById('previewImage');
    const previewVideo = document.getElementById('previewVideo');
    const pdfContainer = document.getElementById('pdfContainer');
    const previewFallback = document.getElementById('previewFallback');
    const fallbackLink = document.getElementById('fallbackLink');
    const modalElement = document.getElementById('previewModal');
    const modalDownloadLink = document.getElementById('modalDownloadLink');

    // Check authentication status on page load
    checkAuthStatus();

    function checkAuthStatus() {
        fetch('/auth/current-user')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.user) {
                    showUserInfo(data.user);
                } else {
                    showLoginButton();
                }
            })
            .catch(error => {
                console.error('Auth check failed:', error);
                showLoginButton();
            });
    }

    function showUserInfo(user) {
        userSection.classList.add('show');
        loginBtn.classList.remove('show');
        userName.textContent = user.name || user.email;
        if (user.avatar_url) {
            userAvatar.src = user.avatar_url;
        } else {
            userAvatar.src = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y';
        }
    }

    function showLoginButton() {
        userSection.classList.remove('show');
        loginBtn.classList.add('show');
    }

    loginBtn.addEventListener('click', function() {
        fetch('/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showUserInfo(data.user);
            } else {
                console.error('Login failed:', data.error);
            }
        })
        .catch(error => console.error('Login error:', error));
    });

    logoutBtn.addEventListener('click', function() {
        fetch('/auth/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showLoginButton();
            }
        })
        .catch(error => console.error('Logout error:', error));
    });

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

    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    let searchTimeout = null;

    // Add search event listeners
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const currentFolderId = new URLSearchParams(window.location.search).get('folder');
            loadFiles(currentFolderId, e.target.value);
        }, 300); // Debounce search for 300ms
    });

    searchButton.addEventListener('click', function() {
        const currentFolderId = new URLSearchParams(window.location.search).get('folder');
        loadFiles(currentFolderId, searchInput.value);
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const currentFolderId = new URLSearchParams(window.location.search).get('folder');
            loadFiles(currentFolderId, searchInput.value);
        }
    });

    function loadFiles(folderId = null, searchQuery = '') {
        showLoadingState();

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

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error loading files');
                }
                renderBreadcrumbs(data.breadcrumbs);
                renderFiles(data.files);
                hideLoadingState();

                // Update URL but keep the search query out of it
                if (!searchQuery) {
                    updateUrl(folderId);
                }
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
                                `<div class="thumbnail-container ${file.mimeType.startsWith('video/') ? 'video-thumbnail' : ''}" 
                                     onclick="previewFile(${JSON.stringify({ 
                                         thumbnail: file.highResThumbnail || file.thumbnailLink,
                                         name: file.name,
                                         downloadUrl: file.downloadUrl,
                                         mimeType: file.mimeType,
                                         webViewLink: file.webViewLink
                                     })})"
                                     style="cursor: pointer;">
                                    <img src="${file.thumbnailLink}" 
                                         alt="${file.name}"
                                         class="card-img-top">
                                    ${file.mimeType.startsWith('video/') ? 
                                        `<div class="video-play-overlay">
                                            <i class="fas fa-play"></i>
                                        </div>` : 
                                        ''
                                    }
                                </div>` : 
                                `<i class="fas fa-file fa-3x text-info"></i>`
                            }
                            <h5 class="card-title text-truncate" title="${file.name}">
                                ${file.name}
                            </h5>
                            <div class="mt-auto">
                                <a href="${file.webViewLink}" target="_blank" class="btn btn-info btn-sm w-100">
                                    <i class="fas fa-external-link-alt me-1"></i> View
                                </a>
                            </div>
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
        const currentSearch = searchInput.value;

        // Add slide-out animation
        filesContainer.classList.add('folder-transition', 'slide-left');

        setTimeout(() => {
            loadFiles(folderId, currentSearch);

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

    // Extract file ID from Google Drive URL safely
    function extractFileId(url) {
        if (!url || typeof url !== 'string') return null;
        const match = url.match(/[-\w]{25,}/);
        return match ? match[0] : null;
    }

    // Function to handle file preview
    window.previewFile = function(fileData) {
        const modalTitle = document.getElementById('previewModalLabel');
        modalTitle.textContent = fileData.name;

        // Reset all preview elements
        previewImage.style.display = 'none';
        previewVideo.style.display = 'none';
        previewFallback.style.display = 'none';
        pdfContainer.style.display = 'none';

        // Clear the PDF container
        pdfContainer.innerHTML = '';

        // Set download link
        modalDownloadLink.href = fileData.downloadUrl;

        if (fileData.mimeType.startsWith('image/')) {
            previewImage.src = fileData.thumbnail;
            previewImage.style.display = 'block';
        } else if (fileData.mimeType.startsWith('video/')) {
            const source = previewVideo.querySelector('source') || document.createElement('source');
            source.src = fileData.downloadUrl;
            source.type = fileData.mimeType;
            if (!previewVideo.querySelector('source')) {
                previewVideo.appendChild(source);
            }
            previewVideo.style.display = 'block';
            previewVideo.load();
        } else if (fileData.mimeType === 'application/pdf') {
            // Create new PDF object element
            const pdfObject = document.createElement('object');
            pdfObject.data = fileData.downloadUrl;
            pdfObject.type = 'application/pdf';
            pdfObject.width = '100%';
            pdfObject.height = '100%';

            // Create fallback link
            const fallbackParagraph = document.createElement('p');
            const fallbackLink = document.createElement('a');
            fallbackLink.href = fileData.downloadUrl;
            fallbackLink.textContent = 'Download';
            fallbackLink.target = '_blank';
            fallbackParagraph.textContent = 'Unable to display PDF file. ';
            fallbackParagraph.appendChild(fallbackLink);
            fallbackParagraph.appendChild(document.createTextNode(' instead.'));

            pdfObject.appendChild(fallbackParagraph);
            pdfContainer.appendChild(pdfObject);
            pdfContainer.style.display = 'block';
        } else {
            previewFallback.style.display = 'block';
            fallbackLink.href = fileData.webViewLink;
        }

        previewModal.show();
    };

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

    // Modal cleanup on hide
    modalElement.addEventListener('hidden.bs.modal', function () {
        // Stop and reset video if it exists
        if (previewVideo) {
            previewVideo.pause();
            previewVideo.currentTime = 0;
            previewVideo.src = '';
            const videoSource = previewVideo.querySelector('source');
            if (videoSource) {
                videoSource.src = '';
            }
        }

        // Reset PDF viewer
        if (pdfContainer) {
            pdfContainer.innerHTML = '';
            pdfContainer.style.display = 'none';
        }

        // Reset image preview
        if (previewImage) {
            previewImage.src = '';
            previewImage.style.display = 'none';
        }
    });

    // Initial auth check
    checkAuthStatus();
});