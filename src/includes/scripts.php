<?php
// scripts.php - Contains all JavaScript scripts
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
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
        const modalElement = document.getElementById('previewModal');

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
            if (previewPdf) {
                pdfContainer.innerHTML = ''; //Clear the pdf container
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
    });
</script>

<script type="text/javascript">
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
                    moneyFormat: '%24%7B%7Bamount%7D%7D',
                    options: {
                        "product": {
                            "styles": {
                                "product": {
                                    "transform": "scale(0.75)",
                                    "transform-origin": "top center",
                                    "@media (min-width: 601px)": {
                                        "max-width": "calc(25% - 20px)",
                                        "margin-left": "20px",
                                        "margin-bottom": "50px"
                                    }
                                },
                                "button": {
                                    "font-family": "Helvetica Neue, sans-serif",
                                    "background-color": "var(--custom-primary)",
                                    ":hover": {
                                        "background-color": "var(--custom-primary-hover)"
                                    },
                                    "border-radius": "6px",
                                    "padding": "8px 16px"
                                },
                                "title": {
                                    "color": "var(--custom-icon)"
                                },
                                "price": {
                                    "color": "var(--custom-icon)"
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
                                    "grid-template-columns": "repeat(auto-fill, minmax(200px, 1fr))",
                                    "gap": "1rem",
                                    "@media (min-width: 601px)": {
                                        "margin-left": "-20px"
                                    }
                                }
                            }
                        },
                        "option": {},
                        "cart": {
                            "styles": {
                                "button": {
                                    "background-color": "var(--custom-primary)",
                                    ":hover": {
                                        "background-color": "var(--custom-primary-hover)"
                                    },
                                    "border-radius": "6px"
                                }
                            }
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