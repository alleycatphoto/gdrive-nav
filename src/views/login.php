<?php
// Include the header
include __DIR__ . '/../includes/header.php';
?>
<body>
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="container mx-auto" style="width: 80vw; max-width: 400px;">">
        <div class="login-container mx-auto">
            <div class="text-center mb-4">
                <img src="/attached_assets/logo.png" alt="DNA Distribution Logo" class="logo">
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
                <div class="mb-3 w-80 mx-auto">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3 w-80 mx-auto">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>

                </div>
                    <div class="mt-3 text-center">
                    <button type="submit" class="btn btn-primary w-80 mt-3">Login</button>
                    </div>
            </form>
            <div class="register-link mt-3 text-center">
                Don't have an account? <a href="/register">Register here</a>
            </div>
        </div>
    </div>

</body>
</html>