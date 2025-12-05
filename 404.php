<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="church-bg">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="auth-card max-w-md w-full text-center">
            <div class="text-6xl mb-4">🕊️</div>
            <h1 class="text-4xl font-serif font-bold text-primary mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
            <p class="text-gray-600 mb-8">
                The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
            </p>
            <div class="space-y-3">
                <a href="/church-reservation/login.php" class="btn-primary block">
                    Go to Login
                </a>
                <a href="/church-reservation/" class="btn-secondary block">
                    Go to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
