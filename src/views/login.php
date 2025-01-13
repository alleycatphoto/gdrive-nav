<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DNA Distribution</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
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
    </style>
</head>
<body>
    <div class="login-container">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
