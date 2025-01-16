<?php
// Include the header
include __DIR__ . '/../includes/header.php';
?>
<body>
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="container">
        <div class="welcome-container text-center">
            <div class="user-avatar">
                <?php if ($currentUser['avatar_url']): ?>
                    <img src="<?php echo htmlspecialchars($currentUser['avatar_url']); ?>" 
                         alt="User avatar" 
                         class="rounded-circle"
                         width="100" 
                         height="100">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <h1 class="display-4 mb-4">Welcome, <?php echo htmlspecialchars($currentUser['first_name'] ?: 'Valued Customer'); ?>!</h1>
            <p class="lead mb-5">Thank you for joining DNA Distribution. Here's what you can do:</p>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-folder-open feature-icon"></i>
                            <h5 class="card-title">Browse Files</h5>
                            <p class="card-text">Resources and documents.</p>
                            <a href="/" class="folder-link">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-search feature-icon"></i>
                            <h5 class="card-title">Search Content</h5>
                            <p class="card-text">Find the exact resources you need.</p>
                            <a href="/?search=true" class="folder-link">Search Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-cog feature-icon"></i>
                            <h5 class="card-title">Manage Profile</h5>
                            <p class="card-text">Update your account settings and preferences.</p>
                            <a href="#" class="folder-link">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <div class="collection-header">
                <h4>Featured Products</h4>
            </div>
            <div id='collection-component-1736831470697'>
                <!-- Shopify buy button will be injected here -->
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/scripts.php'; ?>
</body>
</html>