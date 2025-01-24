<?php
// Get current user if not already passed
if (!isset($currentUser)) {
    $authService = new \App\Services\AuthService();
    $currentUser = $authService->getCurrentUser();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="/attached_assets/logo.png" alt="DNA Distribution Logo"><br/>
             CUSTOMER RESOURCES
        </a>
        <div class="ms-auto d-flex align-items-center">
            <?php if ($currentUser): ?>
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
            <?php endif; ?>
        </div>
    </div>
</nav>
