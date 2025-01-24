<?php
// styles.php - Contains all CSS styles for the application
?>
<style>
    :rootold {
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
    :root {
      --custom-bg: #d1c8d1;
      --custom-bg-lighter: #654d6657;
      --custom-bg-darker: #7f6b7fc2;
      --custom-primary: #74507687;
      --custom-primary-hover: #856087a3;
      --custom-secondary: #ecd5ec9c;
      --custom-secondary-hover: #f6def6ed;
      --custom-icon: #574057;
      --custom-icon-hover: #714971;
    }
    body {
        background-color: var(--custom-bg);
        color: var(--custom-icon);
    }
    a {
        color: var(--custom-icon);
    }
    .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
      margin-top: 0;
      margin-bottom: .5rem;
      font-weight: 500;
      line-height: 1.2;
      color: var(--custom-icon);
    }
    a:hover{
        color: var(--custom-primary-hover);
    }
    
    .navbar {
        background-color: var(--custom-bg-darker) !important;
        justfy-content: center;
    }

    .navbar-brand img {
        height: 30px;
        width: auto;
        margin-right: 10px;
        justfy-content: center;

    }

    .navbar-brand {
        justfy-content: center;
        font-size: 0.8rem;
    }
    .container {
        background-color: var(--custom-bg-lighter);
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    /* Breadcrumb styling */
    .breadcrumb {
        background-color: var(--custom-bg-darker);
        padding: 0.75rem 1rem;
        border-radius: 0.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .breadcrumb-item {
        transition: opacity 0.3s ease-in-out;
        opacity: 0;
        animation: fadeIn 0.5s forwards;
        font-size: 0.9rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: var(--custom-icon);
        opacity: 0.5;
        padding: 0 0.5rem;
        float: none;
        line-height: inherit;
    }

    .breadcrumb-item a,
    .breadcrumb-item.active {
        color: var(--custom-icon);
        text-decoration: none;
        padding: 0.25rem 0.75rem;
        background-color: var(--custom-secondary);
        border-radius: 0.25rem;
        transition: background-color 0.2s;
        display: inline-block;
        line-height: 1.5;
    }

    .breadcrumb-item a:hover {
        background-color: var(--custom-secondary-hover);
        color: var(--custom-icon);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Thumbnail Card Styling */
    .card {
        background-color: var(--custom-secondary);
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        will-change: transform;
        color: var(--custom-icon);
    }

    .card:hover {
        background-color: var(--custom-secondary-hover);
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
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

    /* Action Buttons */
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
    .btn-close {
        background-color: #f7b0f7 !important;
    }
    .btn-close-white {
        background-color: #f7b0f7 !important;
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

    /* Thumbnail Container */
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

    /* Video Thumbnail */
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

    /* Modal Styles */
    .modal-content {
        max-width: 90vw;
        margin: 0 auto;
        background-color: var(--custom-bg-darker) !important;
        border: 1px solid var(--custom-primary);
        position: relative;
        display: flex
    ;
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
        background-color: #d1c8d1 !important;
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
    display: flex
;
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

    /* Shopify Buy Button Customization */
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

    /* Responsive Design */
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
    .modal-body {
        background-color: #161116;
        padding: 0;
        height: calc(90vh - 120px);
        overflow: hidden;
        display: flex
    ;
        justify-content: center;
        align-items: center;
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
    .modal-content {
        max-width: 90vw;
        margin: 0 auto;
        background-color: var(--custom-bg-darker) !important;
        border: 1px solid var(--custom-primary);
        position: relative;
        display: flex
    ;
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
    .pdf-container {
        width: 100%;
        height: calc(90vh - 120px);
        background: #161116;
        display: flex
    ;
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
    }
    .form-control:focus {
        color: var(--bs-body-color);
        background-color: #2f212d;
        border-color: #8f758f;
        outline: 0;
        box-shadow: 0 0 0 .25rem rgb(119 91 119 / 65%);
    }

    .form-control {
        display: block;
        width: 100%;
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-color: #433243;
        background-clip: padding-box;
        border: var(--bs-border-width) solid #c397c3ba;
        border-radius: var(--bs-border-radius);
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
    .form-control:-internal-autofill-selected {
        appearance: menulist-button;
        background-image: none !important;
        background-color: light-dark(rgb(147 111 142 / 63%), rgb(150 105 159 / 67%)) !important;
    }
    .logo {
      vertical-align: middle;
      width: 60%;
      height: auto;
    }
    .row {
      justify-content: center;
    }
    .alert-info {
      --bs-alert-color: #e3c1d9;
      --bs-alert-bg: var(--custom-bg-darker);
      --bs-alert-border-color: var(--custom-bg);
      --bs-alert-link-color: var(--custom-lighter);
    }
    .position-fixed {
        position: absolute !important;
    }
</style>