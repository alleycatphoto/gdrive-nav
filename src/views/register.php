<?php
// Include the header
include __DIR__ . '/../includes/header.php';
?>
<body>
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="container mx-auto" style="width: 30vw;">
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

            <form id="registerForm" action="/auth/register" method="POST" novalidate>
                <div class="mb-3 w-80 mx-auto">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           required 
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <div class="validation-feedback" id="emailFeedback"></div>
                </div>
                <div class="mb-3 w-80 mx-auto">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required 
                           minlength="8">
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="validation-feedback" id="passwordFeedback"></div>
                </div>
                <div class="mb-3 w-80 mx-auto">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="confirmPassword" 
                           required>
                    <div class="validation-feedback" id="confirmPasswordFeedback"></div>
                </div>
                <div class="mb-3 w-80 mx-auto">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" 
                           class="form-control" 
                           id="first_name" 
                           name="first_name">
                </div>
                <div class="mb-3 w-80 mx-auto">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" 
                           class="form-control" 
                           id="last_name" 
                           name="last_name">
                </div>
                <div class="mt-3 text-center">
                <button type="submit" class="btn btn-primary w-50" id="submitBtn">Register</button>
                     </div>
            </form>
            <div class="auth-link mt-3 w-80 mx-auto text-center">
                Already have an account? <a href="/login">Login here</a>
            </div>
        </div>
    </div>

</body>
</html>