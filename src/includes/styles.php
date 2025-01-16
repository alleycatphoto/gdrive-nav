<?php
// styles.php - Contains all CSS styles for the application
?>
<style>
    /* Dark theme variables (default) */
    :root[data-bs-theme="dark"] {
        --custom-bg: #544055;
        --custom-bg-lighter: #654d66;
        --custom-bg-darker: #443344;
        --custom-primary: #745076;
        --custom-primary-hover: #856087;
        --custom-secondary: #493849;
        --custom-secondary-hover: #5a495a;
        --custom-icon: #b996b9;
        --custom-icon-hover: #d2b9d2;
    }

    /* Light theme variables */
    :root[data-bs-theme="light"] {
        --custom-bg: #f8f9fa;
        --custom-bg-lighter: #ffffff;
        --custom-bg-darker: #e9ecef;
        --custom-primary: #745076;
        --custom-primary-hover: #856087;
        --custom-secondary: #e2e3e5;
        --custom-secondary-hover: #d3d4d5;
        --custom-icon: #745076;
        --custom-icon-hover: #856087;
    }

    /* Default theme (dark) */
    :root {
        --custom-bg: #544055;
        --custom-bg-lighter: #654d66;
        --custom-bg-darker: #443344;
        --custom-primary: #745076;
        --custom-primary-hover: #856087;
        --custom-secondary: #493849;
        --custom-secondary-hover: #5a495a;
        --custom-icon: #b996b9;
        --custom-icon-hover: #d2b9d2;
    }

    /* Global styles with theme transitions */
    * {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    body {
        background-color: var(--custom-bg);
        color: var(--custom-icon);
    }

    a {
        color: var(--custom-icon);
    }

    a:hover {
        color: var(--custom-primary-hover);
    }

    .navbar {
        background-color: var(--custom-bg-darker) !important;
        justify-content: center;
    }

    .navbar-brand img {
        height: 30px;
        width: auto;
        margin-right: 10px;
    }

    .container {
        background-color: var(--custom-bg-lighter);
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    /* Theme-aware breadcrumb */
    .breadcrumb {
        background-color: var(--custom-bg-darker);
        padding: 0.75rem 1rem;
        border-radius: 0.25rem;
        margin-bottom: 1.5rem;
    }

    .breadcrumb-item a,
    .breadcrumb-item.active {
        color: var(--custom-icon);
        background-color: var(--custom-secondary);
    }

    .breadcrumb-item a:hover {
        background-color: var(--custom-secondary-hover);
        color: var(--custom-icon);
    }

    /* Theme-aware cards */
    .card {
        background-color: var(--custom-secondary);
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    }

    .card:hover {
        background-color: var(--custom-secondary-hover);
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        color: var(--custom-icon);
    }

    /* Theme-aware buttons */
    .action-btn,
    .btn {
        background-color: var(--custom-secondary);
        border: 1px solid var(--custom-icon);
        color: var(--custom-icon);
    }

    .action-btn:hover,
    .btn:hover {
        background-color: var(--custom-icon);
        color: var(--custom-secondary);
        border-color: var(--custom-icon);
    }

    /* Theme-aware forms */
    .form-control {
        background-color: var(--custom-bg-darker);
        border: var(--bs-border-width) solid var(--custom-icon);
        color: var(--custom-icon);
    }

    .form-control:focus {
        background-color: var(--custom-bg);
        border-color: var(--custom-primary);
        box-shadow: 0 0 0 .25rem var(--custom-primary-hover);
    }

    /* Theme-aware dropdown */
    .dropdown-menu {
        background-color: var(--custom-bg-darker);
        border-color: var(--custom-icon);
    }

    .dropdown-item {
        color: var(--custom-icon);
    }

    .dropdown-item:hover {
        background-color: var(--custom-secondary);
        color: var(--custom-icon-hover);
    }

    /* Theme toggle button */
    .theme-toggle {
        color: var(--custom-icon);
        border: none;
        padding: 0.5rem;
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .theme-toggle:hover {
        color: var(--custom-icon-hover);
        transform: rotate(180deg);
    }

    /* Rest of the existing styles remain unchanged */
    .breadcrumb-item + .breadcrumb-item::before {
        color: var(--custom-icon);
        opacity: 0.5;
        padding: 0 0.5rem;
        float: none;
        line-height: inherit;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .card-body {
        display: flex;
        flex-direction: column;
        padding: 1.25rem;
    }
    .card-title {
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--custom-icon);
        cursor: pointer;
        transition: color 0.2s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }
    .card-title:hover {
        color: var(--custom-icon-hover);
    }
    .file-icon {
        font-size: 1rem;
        color: inherit;
        flex-shrink: 0;
    }
    .card-title span {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .action-btn {
        padding: 0.5rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--custom-secondary);
        border: 1px solid var(--custom-icon);
        color: var(--custom-icon);
        text-decoration: none;
        border-radius: 0.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
        font-size: 0.9rem;
    }
    .action-btn:hover {
        background-color: var(--custom-icon);
        color: var(--custom-secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .action-btn:active {
        transform: translateY(0);
    }
    .btn {
        padding: 0.5rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--custom-secondary);
        border: 1px solid var(--custom-icon);
        color: var(--custom-icon);
        text-decoration: none;
        border-radius: 0.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
        font-size: 0.9rem;
    }
    .btn:hover {
        background-color: var(--custom-icon);
        color: var(--custom-secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid var(--custom-icon);
    }
    .btn:active {
        transform: translateY(0);
        background-color: var(--custom-icon);
        color: var(--custom-secondary);
        border: 1px solid var(--custom-icon);
    }
    .btn.disabled, .btn:disabled, fieldset:disabled .btn {
        color: var(--custom-icon);
        pointer-events: none;
        background-color: var(--custom-secondary);
        border-color: var(--custom-secondary);
        opacity: var(--bs-btn-disabled-opacity);
    }
    .btn-secondary {
        padding: 0.5rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--custom-bg);
        border: 1px solid var(--custom-icon);
        color: var(--custom-icon);
        text-decoration: none;
        border-radius: 0.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
        font-size: 0.9rem;
    }
    .btn-secondary :hover {
        background-color: var(--custom-secondary-hover);
        color: var(--custom-secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .btn-secondary:active {
        transform: translateY(0);
        background-color: var(--custom-icon);
        color: var(--custom-secondary);
    }
    .folder-link {
        width: 100%;
        padding: 0.5rem 0.75rem;
        background-color: var(--custom-secondary);
        border: 1px solid var(--custom-icon);
        color: var(--custom-icon);
        border-radius: 0.25rem;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: auto;
    }
    .folder-link:hover {
        background-color: var(--custom-icon);
        color: var(--custom-secondary);
    }
    .thumbnail-container {
        position: relative;
        padding-bottom: 56.25%;
        background-color: var(--custom-bg-darker);
        border-radius: 0.25rem;
        overflow: hidden;
        margin-bottom: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        will-change: transform;
    }
    .thumbnail-container:hover {
        transform: scale(1.02) translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .thumbnail-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: filter 0.3s ease;
    }
    .thumbnail-container:hover img {
        filter: brightness(1.1);
    }
    .video-play-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 3rem;
        color: rgba(255, 255, 255, 0.8);
        background: rgba(0, 0, 0, 0.5);
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0.8;
        backdrop-filter: blur(2px);
    }
    .thumbnail-container:hover .video-play-overlay {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
        background: rgba(0, 0, 0, 0.7);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
    }
    .modal-content {
        max-width: 90vw;
        margin: 0 auto;
        background-color: var(--custom-bg-darker) !important;
        border: 1px solid var(--custom-primary);
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        color: var(--bs-modal-color);
        pointer-events: auto;
        background-color: var(--bs-modal-bg);
        background-clip: padding-box;
        border: var(--bs-modal-border-width) solid var(--bs-modal-border-color);
        border-radius: var(--bs-modal-border-radius);
        outline: 0;
    }
    .modal-header {
        border-bottom-color: var(--custom-primary);
        background-color: var(--custom-bg);
    }
    .modal-body {
        background-color: #161116;
        padding: 0;
        height: calc(90vh - 120px);
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .modal-dialog {
        max-width: 90vw;
        max-height: 90vh;
        margin: 0.5rem auto;
    }
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - var(--bs-modal-margin)* 2);
    }
    .modal-dialog {
        position: relative;
        width: auto;
        margin: var(--bs-modal-margin);
        pointer-events: none;
    }
    .modal-footer {
        border-top-color: var(--custom-primary);
        background-color: var(--custom-bg);
    }
    .collection-header {
        padding: 0 1rem;
    }
    .collection-header h4 {
        color: var(--custom-icon);
        font-size: 1.25rem;
        font-weight: 500;
        margin: 0;
    }
    #collection-component-1736831470697 {
        margin: 2rem 0;
    }
    .shopify-buy__product {
        background-color: var(--custom-bg-darker) !important;
        border-radius: 0.5rem !important;
        overflow: hidden !important;
        transition: transform 0.3s ease-in-out !important;
        transform: scale(0.75) !important;
        transform-origin: top center !important;
    }
    .shopify-buy__product:hover {
        transform: scale(0.75) translateY(-4px) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    }
    .shopify-buy__product-img-wrapper {
        position: relative !important;
        background-color: var(--custom-bg-darker) !important;
        border-radius: 0.25rem 0.25rem 0 0 !important;
        overflow: hidden !important;
    }
    .shopify-buy__product-img {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .shopify-buy__product__title {
        padding: 1rem !important;
        margin: 0 !important;
        color: var(--custom-icon) !important;
        font-size: 0.9rem !important;
    }
    .shopify-buy__product__price {
        padding: 0 1rem 1rem !important;
        color: var(--custom-icon) !important;
        font-size: 0.9rem !important;
    }
    .shopify-buy__btn {
        width: calc(100% - 2rem) !important;
        margin: 0 1rem 1rem !important;
        background-color: var(--custom-primary) !important;
        border: none !important;
        border-radius: 0.25rem !important;
        color: white !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem !important;
        transition: background-color 0.2s !important;
    }
    .shopify-buy__btn:hover {
        background-color: var(--custom-primary-hover) !important;
    }
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
            margin-top: 1rem;
        }
        .card-title {
            font-size: 0.9rem;
        }
        .action-btn,
        .folder-link {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }
        .navbar-brand {
            font-size: 0.8rem;
        }
    }
    .text-center {
        text-align: center !important;
    }
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: var(--bs-modal-padding);
    }
    .modal-body object {
        width: 100%;
        height: 100%;
        display: block;
    }
    .pdf-container {
        width: 100%;
        height: calc(90vh - 120px);
        background: #161116;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .dropdown-menu {
        --bs-dropdown-color: #d2b9d2;
        --bs-dropdown-bg: #544055;
        --bs-dropdown-link-hover-color: #d2b9d2;
        --bs-dropdown-link-hover-bg: #b996b9;
        --bs-dropdown-link-active-color: #d2b9d2;
        --bs-dropdown-link-active-bg: #b996b9;
        position: absolute;
        z-index: var(--bs-dropdown-zindex);
        display: none;
        min-width: var(--bs-dropdown-min-width);
        padding: var(--bs-dropdown-padding-y) var(--bs-dropdown-padding-x);
        margin: 0;
        font-size: var(--bs-dropdown-font-size);
        color: var(--bs-dropdown-color);
        text-align: left;
        list-style: none;
        background-color: var(--bs-dropdown-bg);
        background-clip: padding-box;
        border: var(--bs-dropdown-border-width) solid var(--bs-dropdown-border-color);
        border-radius: var(--bs-dropdown-border-radius);
    }
    .shopify-buy-frame iframe {
        width: 100%;
        height: 300px !important;
    }
    .form-control {
        background-color: #392939;
        border: var(--bs-border-width) solid #836a81;
    }
    .form-control:focus {
        color: var(--bs-body-color);
        background-color: #2f202e;
        border-color: #c5afc5;
        outline: 0;
        box-shadow: 0 0 0 .25rem #b996b9ad;
    }
</style>