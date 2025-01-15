<?php
// Include the header
include __DIR__ . '/../includes/header.php';
?>
<body>
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="container">
        <div class="login-container mx-auto">
            <div class="text-center mb-4">
                <img src="/attached_assets/Cryoskin White Transparent.png" alt="DNA Distribution Logo" class="logo">
            </div>

            <?php if (isset($_SESSION['auth_error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo htmlspecialchars($_SESSION['auth_error']); 
                    unset($_SESSION['auth_error']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="/auth/login" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="register-link mt-3 text-center">
                Don't have an account? <a href="/register">Register here</a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/scripts.php'; ?>
</body>
</html>