<?php
// scripts.php - Contains all JavaScript scripts
?>
<!-- Essential Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<!-- Custom Scripts -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialize all Bootstrap popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        });

        // Initialize preview modal if elements exist
        const previewModalElement = document.getElementById('previewModal');
        if (previewModalElement) {
            try {
                const previewModal = new bootstrap.Modal(previewModalElement);
                const previewImage = document.getElementById('previewImage');
                const previewVideo = document.getElementById('previewVideo');
                const pdfContainer = document.getElementById('pdfContainer');
                const previewPdf = document.getElementById('previewPdf');
                const pdfDownloadLink = document.getElementById('pdfDownloadLink');
                const previewFallback = document.getElementById('previewFallback');
                const fallbackLink = document.getElementById('fallbackLink');

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
                previewModalElement.addEventListener('hidden.bs.modal', function () {
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

                // Make previewFile function globally available
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
            } catch (error) {
                console.error('Error initializing preview modal:', error);
            }
        }

        // Initialize form validation if register form exists
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            initializeRegisterFormValidation();
        }
    });

    // Shopify Buy Button initialization
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
                                    "transition": "transform 0.5s ease-in-out",
                                    "background-color": "#493849",
                                    "@media (min-width: 2000px)": {
                                        "max-width": "calc(25% - 20px)",
                                        "margin-left": "0px",
                                        "margin-bottom": "10px"
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

    // Register form validation initialization
    function initializeRegisterFormValidation() {
        const form = document.getElementById('registerForm');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const submitBtn = document.getElementById('submitBtn');
        const passwordStrength = document.getElementById('passwordStrength');

        const emailFeedback = document.getElementById('emailFeedback');
        const passwordFeedback = document.getElementById('passwordFeedback');
        const confirmPasswordFeedback = document.getElementById('confirmPasswordFeedback');

        function validateEmail() {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const isValid = emailRegex.test(email.value);

            if (email.value === '') {
                email.classList.remove('is-valid', 'is-invalid');
                emailFeedback.textContent = '';
            } else if (isValid) {
                email.classList.remove('is-invalid');
                email.classList.add('is-valid');
                emailFeedback.textContent = 'Email is valid';
            } else {
                email.classList.remove('is-valid');
                email.classList.add('is-invalid');
                emailFeedback.textContent = 'Please enter a valid email address';
            }
            validateForm();
        }

        function validatePassword() {
            const hasMinLength = password.value.length >= 8;
            const hasUpperCase = /[A-Z]/.test(password.value);
            const hasLowerCase = /[a-z]/.test(password.value);
            const hasNumbers = /\d/.test(password.value);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password.value);

            let strength = 0;
            if (hasMinLength) strength++;
            if (hasUpperCase) strength++;
            if (hasLowerCase) strength++;
            if (hasNumbers) strength++;
            if (hasSpecialChar) strength++;

            const strengthText = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
            const strengthColor = ['danger', 'warning', 'info', 'primary', 'success'];

            if (password.value === '') {
                passwordStrength.innerHTML = '';
                password.classList.remove('is-valid', 'is-invalid');
                passwordFeedback.textContent = '';
            } else if (strength < 3) {
                password.classList.remove('is-valid');
                password.classList.add('is-invalid');
                passwordStrength.innerHTML = `
                    <span class="text-${strengthColor[strength-1]}">
                        Password Strength: ${strengthText[strength-1]}
                    </span>
                `;
                passwordFeedback.textContent = 'Password must be at least 8 characters long and contain uppercase, lowercase, numbers, and special characters';
            } else {
                password.classList.remove('is-invalid');
                password.classList.add('is-valid');
                passwordStrength.innerHTML = `
                    <span class="text-${strengthColor[strength-1]}">
                        Password Strength: ${strengthText[strength-1]}
                    </span>
                `;
                passwordFeedback.textContent = 'Password meets requirements';
            }
            validateConfirmPassword();
            validateForm();
        }

        function validateConfirmPassword() {
            if (confirmPassword.value === '') {
                confirmPassword.classList.remove('is-valid', 'is-invalid');
                confirmPasswordFeedback.textContent = '';
            } else if (password.value === confirmPassword.value) {
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
                confirmPasswordFeedback.textContent = 'Passwords match';
            } else {
                confirmPassword.classList.remove('is-valid');
                confirmPassword.classList.add('is-invalid');
                confirmPasswordFeedback.textContent = 'Passwords do not match';
            }
            validateForm();
        }

        function validateForm() {
            const isEmailValid = email.classList.contains('is-valid');
            const isPasswordValid = password.classList.contains('is-valid');
            const isConfirmPasswordValid = confirmPassword.classList.contains('is-valid');

            submitBtn.disabled = !(isEmailValid && isPasswordValid && isConfirmPasswordValid);
        }

        if (email) email.addEventListener('input', validateEmail);
        if (password) password.addEventListener('input', validatePassword);
        if (confirmPassword) confirmPassword.addEventListener('input', validateConfirmPassword);

        form.addEventListener('submit', function(e) {
            if (!submitBtn.disabled) {
                return true;
            }
            e.preventDefault();
            return false;
        });
    }
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
                                    "transition": "transform 0.5s ease-in-out",
                                    "background-color": "#493849",
                                    "@media (min-width: 2000px)": {
                                        "max-width": "calc(25% - 20px)",
                                        "margin-left": "0px",
                                        "margin-bottom": "10px"
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