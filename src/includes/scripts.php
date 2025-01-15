<?php
// scripts.php - Contains all JavaScript scripts
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all Bootstrap tooltips with enhanced options
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true,
                delay: { show: 200, hide: 100 },
                container: 'body'
            });
        });

        // Initialize sharing tooltips
        initializeSharingTooltips();

        // Initialize preview modal
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const previewImage = document.getElementById('previewImage');
        const previewVideo = document.getElementById('previewVideo');
        const pdfContainer = document.getElementById('pdfContainer');
        const previewPdf = document.getElementById('previewPdf');
        const pdfDownloadLink = document.getElementById('pdfDownloadLink');
        const previewFallback = document.getElementById('previewFallback');
        const fallbackLink = document.getElementById('fallbackLink');
        const modalElement = document.getElementById('previewModal');
        const modalShareBtn = document.getElementById('modalShareBtn');

        // Preload management
        let preloadQueue = [];
        let isPreloading = false;
        const maxPreloadQueueSize = 3;

        function addToPreloadQueue(fileId, mimeType) {
            if (!preloadQueue.some(item => item.fileId === fileId) &&
                preloadQueue.length < maxPreloadQueueSize) {
                preloadQueue.push({ fileId, mimeType });
                processPreloadQueue();
            }
        }

        // Add click handler for modal share button
        if (modalShareBtn) {
            modalShareBtn.addEventListener('click', function() {
                const fileId = this.getAttribute('data-file-id');
                if (fileId) {
                    copyShareLink(fileId);
                }
            });
        }

        function processPreloadQueue() {
            if (isPreloading || preloadQueue.length === 0) return;

            isPreloading = true;
            const { fileId, mimeType } = preloadQueue[0];

            if (mimeType.startsWith('video/')) {
                const preloadVideo = document.createElement('video');
                preloadVideo.preload = 'metadata';
                preloadVideo.src = getProxyUrl(fileId);

                preloadVideo.addEventListener('loadedmetadata', () => {
                    preloadQueue.shift();
                    isPreloading = false;
                    processPreloadQueue();
                });

                preloadVideo.addEventListener('error', () => {
                    preloadQueue.shift();
                    isPreloading = false;
                    processPreloadQueue();
                });
            } else {
                preloadQueue.shift();
                isPreloading = false;
                processPreloadQueue();
            }
        }

        // Add modal close event listener
        modalElement.addEventListener('hidden.bs.modal', function () {
            if (previewVideo) {
                previewVideo.pause();
                previewVideo.currentTime = 0;
                previewVideo.src = '';
                const videoSource = previewVideo.querySelector('source');
                if (videoSource) {
                    videoSource.src = '';
                }
            }
            if (pdfContainer) {
                pdfContainer.innerHTML = '';
                pdfContainer.style.display = 'none';
            }
        });

        // Function to get proxy URL for a file
        function getProxyUrl(fileId) {
            return `/proxy/${fileId}`;
        }

        // Function to extract file ID from Google Drive URL
        function extractFileId(url) {
            if (!url) return null;
            const match = url.match(/[-\w]{25,}/);
            return match ? match[0] : null;
        }

        // Generate Google Drive sharing link
        function generateGoogleDriveShareLink(fileId) {
            return `https://drive.google.com/file/d/${fileId}/view?usp=sharing`;
        }

        // Copy text to clipboard
        async function copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (err) {
                console.error('Failed to copy text:', err);
                return false;
            }
        }

        // Function to preview file - make it globally available
        window.previewFile = function(props) {
            if (typeof props === 'string') {
                try {
                    props = JSON.parse(props);
                } catch (e) {
                    console.error('Error parsing preview properties:', e);
                    return;
                }
            }

            const { thumbnail, name, downloadUrl, mimeType, webViewLink } = props;
            const modalTitle = document.getElementById('previewModalLabel');
            const downloadLink = document.getElementById('modalDownloadLink');
            const fileId = extractFileId(downloadUrl);
            const proxyUrl = fileId ? getProxyUrl(fileId) : downloadUrl;

            // Update modal share button
            if (modalShareBtn && fileId) {
                modalShareBtn.setAttribute('data-file-id', fileId);
            }

            modalTitle.textContent = name;
            downloadLink.href = downloadUrl;

            // Reset all preview elements
            previewImage.style.display = 'none';
            previewVideo.style.display = 'none';
            pdfContainer.style.display = 'none';
            previewFallback.style.display = 'none';

            // Reset video element
            previewVideo.pause();
            previewVideo.currentTime = 0;
            previewVideo.src = '';
            const videoSource = previewVideo.querySelector('source');
            if (videoSource) {
                videoSource.src = '';
            }

            // Show modal
            previewModal.show();

            setTimeout(() => {
                if (mimeType.startsWith('image/')) {
                    previewImage.src = thumbnail || '';
                    previewImage.style.display = 'block';
                } else if (mimeType.startsWith('video/')) {
                    videoSource.src = proxyUrl;
                    videoSource.type = mimeType;
                    previewVideo.src = proxyUrl;
                    previewVideo.style.display = 'block';

                    const loadHandler = function() {
                        previewVideo.removeEventListener('loadeddata', loadHandler);
                        previewVideo.play().catch(function(error) {
                            console.error('Error playing video:', error);
                            previewFallback.style.display = 'block';
                            previewVideo.style.display = 'none';
                        });
                    };

                    previewVideo.addEventListener('loadeddata', loadHandler);
                    previewVideo.load();
                } else if (mimeType === 'application/pdf') {
                    const newPdfObject = document.createElement('object');
                    newPdfObject.id = 'previewPdf';
                    newPdfObject.data = proxyUrl;
                    newPdfObject.type = 'application/pdf';

                    pdfContainer.innerHTML = '';
                    pdfContainer.appendChild(newPdfObject);
                    pdfContainer.style.display = 'block';
                } else {
                    previewFallback.style.display = 'block';
                    fallbackLink.href = webViewLink;
                }
            }, 300);
        };

        // Add hover event listeners to video thumbnails for preloading
        document.querySelectorAll('.video-thumbnail').forEach(thumbnail => {
            thumbnail.addEventListener('mouseenter', function() {
                const previewProps = this.closest('[onclick]').getAttribute('onclick');
                if (!previewProps) return;

                const match = previewProps.match(/previewFile\((.*)\)/);
                if (!match || !match[1]) return;

                try {
                    const props = JSON.parse(match[1]);
                    const fileId = extractFileId(props.downloadUrl);
                    if (fileId) {
                        addToPreloadQueue(fileId, props.mimeType);
                    }
                } catch (e) {
                    console.error('Error parsing preview properties for preload:', e);
                }
            });
        });

        // Add click handlers for thumbnail share buttons
        document.querySelectorAll('.thumbnail-share-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const fileId = this.getAttribute('data-file-id');
                if (fileId) {
                    copyShareLink(fileId);
                }
            });
        });

        // Add click handlers for share buttons
        document.querySelectorAll('.file-actions .action-btn[data-file-id]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const fileId = this.getAttribute('data-file-id');
                if (fileId) {
                    copyShareLink(fileId, e); // Pass the event here
                }
            });
        });
    });

    // Initialize sharing tooltips functionality
    function initializeSharingTooltips() {
        document.querySelectorAll('[data-sharing-tooltip]').forEach(element => {
            const fileId = element.dataset.fileId;
            const fileName = element.dataset.fileName;
            const fileType = element.dataset.fileType;

            // Create tooltip content
            const tooltipContent = generateSharingTooltipContent(fileId, fileName, fileType);

            // Initialize Bootstrap tooltip with custom content
            new bootstrap.Tooltip(element, {
                html: true,
                title: tooltipContent,
                placement: 'auto',
                trigger: 'hover focus',
                delay: { show: 200, hide: 100 },
                container: 'body'
            });

            // Add event listeners for share actions
            element.addEventListener('shown.bs.tooltip', () => {
                const tooltip = bootstrap.Tooltip.getInstance(element);
                const tooltipElement = tooltip._element;

                // Add click handlers for share buttons
                const copyLinkBtn = tooltipElement.querySelector('.copy-link-btn');
                if (copyLinkBtn) {
                    copyLinkBtn.addEventListener('click', () => copyShareLink(fileId));
                }
            });
        });
    }

    // Generate tooltip content based on file type and sharing options
    function generateSharingTooltipContent(fileId, fileName, fileType) {
        const shareLink = generateShareLink(fileId);

        return `
            <div class="sharing-tooltip-content">
                <div class="sharing-header">
                    <i class="fas ${getFileTypeIcon(fileType)} me-2"></i>
                    <span class="file-name">${fileName}</span>
                </div>
                <div class="sharing-actions mt-2">
                    <button class="btn btn-sm btn-outline-light copy-link-btn" data-file-id="${fileId}">
                        <i class="fas fa-link me-1"></i> Copy Link
                    </button>
                    <button class="btn btn-sm btn-outline-light share-email-btn ms-2" data-file-id="${fileId}">
                        <i class="fas fa-envelope me-1"></i> Email
                    </button>
                </div>
                <div class="sharing-footer mt-2">
                    <small class="text-muted">Click to copy or share</small>
                </div>
            </div>
        `;
    }

    // Helper function to get appropriate icon for file type
    function getFileTypeIcon(fileType) {
        const iconMap = {
            'pdf': 'fa-file-pdf',
            'doc': 'fa-file-word',
            'docx': 'fa-file-word',
            'xls': 'fa-file-excel',
            'xlsx': 'fa-file-excel',
            'jpg': 'fa-file-image',
            'jpeg': 'fa-file-image',
            'png': 'fa-file-image',
            'mp4': 'fa-file-video',
            'zip': 'fa-file-archive',
            'default': 'fa-file'
        };

        return iconMap[fileType.toLowerCase()] || iconMap.default;
    }

    // Generate shareable link for a file
    function generateShareLink(fileId) {
        const baseUrl = window.location.origin;
        return `${baseUrl}/share?id=${fileId}`;
    }

    // Generate social media sharing URLs
    function generateSocialMediaUrls(fileId, fileName) {
        const shareUrl = encodeURIComponent(generateShareLink(fileId));
        const shareTitle = encodeURIComponent(`Check out ${fileName} on DNA Distribution`);

        return {
            twitter: `https://twitter.com/intent/tweet?url=${shareUrl}&text=${shareTitle}`,
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`,
            linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}`,
            email: `mailto:?subject=${shareTitle}&body=${shareUrl}`
        };
    }

    // Show social sharing popup
    function showSocialSharingPopup(fileId, event) {
        const clickedButton = event ? event.currentTarget : document.querySelector(`[data-file-id="${fileId}"]`);
        const fileName = clickedButton.closest('.card').querySelector('.card-title span').getAttribute('title');
        const urls = generateSocialMediaUrls(fileId, fileName);
        const shareLink = generateShareLink(fileId);

        const popupContent = `
            <div class="social-sharing-popup">
                <div class="d-flex flex-column gap-2">
                    <button class="btn btn-outline-light copy-link-btn" data-clipboard-text="${shareLink}">
                        <i class="fas fa-link me-2"></i>Copy Link
                    </button>
                    <div class="dropdown-divider"></div>
                    <a href="${urls.twitter}" target="_blank" class="btn btn-outline-info" onclick="window.open(this.href, '_blank', 'width=550,height=420'); return false;">
                        <i class="fab fa-twitter me-2"></i>Share on Twitter
                    </a>
                    <a href="${urls.facebook}" target="_blank" class="btn btn-outline-primary" onclick="window.open(this.href, '_blank', 'width=550,height=420'); return false;">
                        <i class="fab fa-facebook me-2"></i>Share on Facebook
                    </a>
                    <a href="${urls.linkedin}" target="_blank" class="btn btn-outline-info" onclick="window.open(this.href, '_blank', 'width=550,height=520'); return false;">
                        <i class="fab fa-linkedin me-2"></i>Share on LinkedIn
                    </a>
                    <a href="${urls.email}" class="btn btn-outline-secondary">
                        <i class="fas fa-envelope me-2"></i>Share via Email
                    </a>
                </div>
            </div>
        `;

        const existingPopup = document.querySelector('.social-sharing-popup');
        if (existingPopup) {
            existingPopup.remove();
        }

        const popup = document.createElement('div');
        popup.className = 'position-absolute';
        popup.style.zIndex = '9999';
        popup.innerHTML = popupContent;

        // Position popup relative to clicked button
        clickedButton.style.position = 'relative';
        clickedButton.appendChild(popup);

        // Style the popup and its buttons
        const popupElement = popup.querySelector('.social-sharing-popup');
        popupElement.style.minWidth = '200px';
        popupElement.style.backgroundColor = 'var(--bs-dark)';
        popupElement.style.border = '1px solid var(--bs-secondary)';
        popupElement.style.borderRadius = '0.375rem';
        popupElement.style.padding = '0.5rem';
        popupElement.style.position = 'absolute';
        popupElement.style.right = '0';
        popupElement.style.top = '100%';
        popupElement.style.marginTop = '0.5rem';
        popupElement.style.zIndex = '9999';
        popupElement.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';

        // Style all buttons in the popup
        popup.querySelectorAll('.btn').forEach(btn => {
            btn.style.whiteSpace = 'nowrap';
            btn.style.width = '100%';
            btn.style.textAlign = 'left';
            btn.style.display = 'flex';
            btn.style.alignItems = 'center';
        });

        // Add click handler for copy link button
        const copyButton = popup.querySelector('.copy-link-btn');
        copyButton.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(shareLink);
                showToast('Link copied to clipboard!');
                popup.remove();
            } catch (err) {
                console.error('Failed to copy link:', err);
                showToast('Failed to copy link', 'error');
            }
        });

        // Close popup when clicking outside
        const closePopup = (e) => {
            if (!popup.contains(e.target) && !clickedButton.contains(e.target)) {
                popup.remove();
                document.removeEventListener('click', closePopup);
            }
        };

        // Add delay to prevent immediate closure
        setTimeout(() => {
            document.addEventListener('click', closePopup);
        }, 100);

        // Auto-hide after 10 seconds
        setTimeout(() => {
            if (document.body.contains(popup)) {
                popup.remove();
                document.removeEventListener('click', closePopup);
            }
        }, 10000);
    }

    // Update copyShareLink function to pass the event
    async function copyShareLink(fileId, event) {
        showSocialSharingPopup(fileId, event);
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        const toastElement = document.createElement('div');
        toastElement.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');

        toastElement.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        toastContainer.appendChild(toastElement);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Create toast container if it doesn't exist
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1050';
        document.body.appendChild(container);
        return container;
    }


    /*<![CDATA[*/
    (function () {
        var scriptURL = 'https://sdks.shopifycdn.com/buy-button/latest/buy-button-storefront.min.js';
        if (window.ShopifyBuy) {
            if (window.ShopifyBuy.UI) {
                ShopifyBuyInit();
            } else {
                loadScript();
            }
        } else {
            loadScript();
        }
        function loadScript() {
            var script = document.createElement('script');
            script.async = true;
            script.src = scriptURL;
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(script);
            script.onload = ShopifyBuyInit;
        }
        function ShopifyBuyInit() {
            var client = ShopifyBuy.buildClient({
                domain: 'b570c3-8b.myshopify.com',
                storefrontAccessToken: '0503f7e334e120cf50858c5491cf8491',
            });
            ShopifyBuy.UI.onReady(client).then(function (ui) {
                ui.createComponent('collection', {
                    id: '313578782870',
                    node: document.getElementById('collection-component-1736831470697'),
                    moneyFormat: '%24%7B%7D%7D',
                    options: {
                        "product": {

                            "styles": {
                                "product": {
                                    "transform": "scale(0.65)",
                                    "transform-origin": "top center",
                                    "transition": "transform 0.5s ease-in-out",
                                    "padding": "20px",
                                    "color": "#ba95b9",
                                    "border-radius": ".375rem",

                                    "background-color": "#493849",
                                    "@media (min-width: 2000px)": {
                                        "max-width": "calc(25% - 20px)",
                                        "margin-left": "0px",
                                        "margin-bottom": "10px"
                                    },

                                },

                                "title": {
                                    "color": "#ba95b9"
                                },
                                "price": {
                                    "font-size": "20px",
                                    "color": "#9db98d"
                                },
                                "description": {
                                    "margin-top": "30px",
                                    "line-height": "1.65",
                                    "color": "#c3aac2"
                                }
                            },
                            "buttonDestination": "modal",
                            "contents": {
                                "options": false
                            },
                            "text": {
                                "button": "View Details"
                            }
                        },
                        "productSet": {
                            "styles": {
                                "products": {
                                    "display": "grid",
                                    "grid-template-columns": "repeat(auto-fill, minmax(200px, 0fr))",
                                    "@media (min-width: 601px)": {
                                        "margint": "10px"
                                    }
                                }
                            }
                        },
                        "modal": {
                            "styles": {
                                "modal": {
                                    "background-color": "#544055"
                                }
                            }
                        },
                        "option": {
                            "styles": {
                                "label": {
                                    "font-family": "Open Sans, sans-serif",
                                    "color": "#d2abd4"
                                },
                                "select": {
                                    "font-family": "Open Sans, sans-serif"
                                }
                            },
                            "googleFonts": [
                                "Open Sans"
                            ]
                        },
                        "cart": {
                            "styles": {
                                "button": {
                                    "font-family": "Open Sans, sans-serif",
                                    "font-size": "13px",
                                    "padding-top": "14.5px",
                                    "padding-bottom": "14.5px",
                                    "color": "#efd8f9",
                                    ":hover": {
                                        "color": "#efd8f9",
                                        "background-color": "#473348"
                                    },
                                    "background-color": "#4f3950",
                                    ":focus": {
                                        "background-color": "#473348"
                                    },
                                    "border-radius": "2px"
                                },
                                "title": {
                                    "color": "#e1e1e1"
                                },
                                "header": {
                                    "color": "#e1e1e1"
                                },
                                "lineItems": {
                                    "color": "#e1e1e1"
                                },
                                "subtotalText": {
                                    "color": "#e1e1e1"
                                },
                                "subtotal": {
                                    "color": "#e1e1e1"
                                },
                                "notice": {
                                    "color": "#e1e1e1"
                                },
                                "currency": {
                                    "color": "#e1e1e1"
                                },
                                "close": {
                                    "color": "#e1e1e1",
                                    ":hover": {
                                        "color": "#e1e1e1"
                                    }
                                },
                                "empty": {
                                    "color": "#e1e1e1"
                                },
                                "noteDescription": {
                                    "color": "#e1e1e1"
                                },
                                "discountText": {
                                    "color": "#e1e1e1"
                                },
                                "discountIcon": {
                                    "fill": "#e1e1e1"
                                },
                                "discountAmount": {
                                    "color": "#e1e1e1"
                                },
                                "cart": {
                                    "background-color": "#544055"
                                },
                                "footer": {
                                    "background-color": "#544055"
                                }
                            },
                            "text": {
                                "total": "Subtotal",
                                "button": "Checkout"
                            },
                            "contents": {
                                "note": true
                            },
                            "popup": false,
                            "googleFonts": [
                                "Open Sans"
                            ]
                        },
                        "toggle": {
                            "styles": {
                                "toggle": {
                                    "background-color": "var(--custom-primary)",
                                    ":hover": {
                                        "background-color": "var(--custom-primary-hover)"
                                    }
                                }
                            }
                        }
                    }
                });
            });
        }
    })();
    /*]]>*/
</script>