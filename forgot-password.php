<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Church Reservation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-[#002B5C]">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="auth-card max-w-md w-full">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-serif text-primary mb-2">🕊️</h1>
                <h2 class="text-3xl font-serif font-bold text-primary">Reset Password</h2>
                <p class="mt-2 text-gray-600">Enter your email to reset your password</p>
            </div>

            <!-- Step 1: Email Verification -->
            <div id="step1" class="space-y-6">
                <form id="emailCheckForm" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div class="text-center">
                        <div class="flex space-x-2">
                            <div class="w-1/3 h-2 bg-primary rounded"></div>
                            <div class="w-1/3 h-2 bg-gray-300 rounded"></div>
                            <div class="w-1/3 h-2 bg-gray-300 rounded"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Step 1 of 2</p>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary w-full">
                            Check Email
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="login.php" class="text-sm font-medium text-primary hover:text-accent">
                            ← Back to Login
                        </a>
                    </div>
                </form>
            </div>

            <!-- Step 2: Reset Password (Hidden by default) -->
            <div id="step2" class="space-y-6 hidden">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Email verified! Please enter your new password.
                </div>

                <form id="resetPasswordForm" method="POST" action="api/auth.php" class="space-y-6">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" id="verified_email" name="email">

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                        <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                    </div>

                    <div>
                        <label for="confirm_new_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required minlength="6"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div class="text-center">
                        <div class="flex space-x-2">
                            <div class="w-1/3 h-2 bg-primary rounded"></div>
                            <div class="w-1/3 h-2 bg-primary rounded"></div>
                            <div class="w-1/3 h-2 bg-gray-300 rounded"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Step 2 of 2</p>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary w-full">
                            Reset Password
                        </button>
                    </div>
                    <div class="text-center">
                        <a href="landing.php" class="text-sm font-medium text-primary hover:text-accent">
                            ← Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Handle email check
        document.getElementById('emailCheckForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;

            try {
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=check_email&email=${encodeURIComponent(email)}`
                });

                const result = await response.json();

                if (result.success) {
                    // Email exists, show step 2
                    document.getElementById('step1').classList.add('hidden');
                    document.getElementById('step2').classList.remove('hidden');
                    document.getElementById('verified_email').value = email;
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error checking email. Please try again.');
            }
        });
    </script>
</body>
</html>
