<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - DNA Distribution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --custom-bg: #544055;
            --custom-bg-lighter: #654d66;
            --custom-bg-darker: #443344;
            --custom-primary: #745076;
            --custom-primary-hover: #856087;
        }

        body {
            background-color: var(--custom-bg);
            min-height: 100vh;
        }

        .navbar {
            background-color: var(--custom-bg-darker) !important;
        }

        .navbar-brand img {
            height: 30px;
            width: auto;
            margin-right: 10px;
        }

        .welcome-container {
            background-color: var(--custom-bg-lighter);
            border-radius: 0.5rem;
            padding: 2rem;
            margin-top: 2rem;
        }

        .feature-card {
            background-color: var(--custom-bg-darker);
            border: none;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2rem;
            color: var(--custom-primary);
            margin-bottom: 1rem;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--custom-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .user-avatar i {
            font-size: 3rem;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="/attached_assets/Cryoskin White Transparent.png" alt="DNA Distribution Logo">
                CUSTOMER RESOURCES
            </a>
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-link nav-link dropdown-toggle d-flex align-items-center gap-2" 
                            type="button" 
                            id="userDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <?php if ($currentUser['avatar_url']): ?>
                            <img src="<?php echo htmlspecialchars($currentUser['avatar_url']); ?>" 
                                 alt="User avatar" 
                                 class="rounded-circle"
                                 width="32" 
                                 height="32">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-2x"></i>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($currentUser['first_name'] ?: $currentUser['email']); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="/auth/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

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
                            <p class="card-text">Access our comprehensive collection of resources and documents.</p>
                            <a href="/" class="btn btn-primary">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-search feature-icon"></i>
                            <h5 class="card-title">Search Content</h5>
                            <p class="card-text">Quickly find the exact resources you need.</p>
                            <a href="/?search=true" class="btn btn-primary">Search Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-cog feature-icon"></i>
                            <h5 class="card-title">Manage Profile</h5>
                            <p class="card-text">Update your account settings and preferences.</p>
                            <a href="#" class="btn btn-primary">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
