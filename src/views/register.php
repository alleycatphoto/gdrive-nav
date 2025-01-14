<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DNA Distribution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --custom-bg: #544055;
            --custom-bg-lighter: #2e212f;
            --custom-bg-darker: #443344;
            --custom-primary: #745076;
            --custom-primary-hover: #856087;
        }

        body {
            background-color: var(--custom-bg);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            background-color: var(--custom-bg-lighter);
            border-radius: 0.5rem;
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--custom-primary);
            border-color: var(--custom-primary);
        }

        .btn-primary:hover {
            background-color: var(--custom-primary-hover);
            border-color: var(--custom-primary-hover);
        }

        .logo {
            width: 200px;
            margin-bottom: 2rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: var(--custom-primary);
            text-decoration: none;
        }

        .login-link a:hover {
            color: var(--custom-primary-hover);
            text-decoration: underline;
        }

        .password-strength {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .validation-feedback {
            display: none;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid ~ .validation-feedback {
            display: block;
            color: var(--bs-danger);
        }

        .is-valid ~ .validation-feedback {
            display: block;
            color: var(--bs-success);
        }
        .btn.disabled, .btn:disabled, fieldset:disabled .btn {
        color: var(--bs-btn-disabled-color);
        pointer-events: none;
        background-color: #554055;
        border-color: #2f212f;
        opacity: var(--bs-btn-disabled-opacity);
    </style>
</head>
<body>
    <div class="register-container">
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

        <form id="registerForm" action="/auth/register" method="POST" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       name="email" 
                       required 
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <div class="validation-feedback" id="emailFeedback"></div>
            </div>
            <div class="mb-3">
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
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" 
                       class="form-control" 
                       id="confirmPassword" 
                       required>
                <div class="validation-feedback" id="confirmPasswordFeedback"></div>
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" 
                       class="form-control" 
                       id="first_name" 
                       name="first_name">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" 
                       class="form-control" 
                       id="last_name" 
                       name="last_name">
            </div>
            <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="/login">Login here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            email.addEventListener('input', validateEmail);
            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validateConfirmPassword);

            form.addEventListener('submit', function(e) {
                if (!submitBtn.disabled) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        });
    </script>
</body>
</html>