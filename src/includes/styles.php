<?php
// styles.php - Contains all CSS styles for the application
?>
<style>
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

    body {
        background-color: var(--custom-bg);
    }

    .navbar {
        background-color: var(--custom-bg-darker) !important;
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

    /* File cards */
    .card {
        background-color: var(--custom-secondary);
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        will-change: transform;
    }

    .card:hover {
        background-color: var(--custom-secondary-hover);
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    /* Rest of existing styles... */
    <?php include 'shopify_styles.php'; ?>
</style>
