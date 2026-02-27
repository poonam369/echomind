<?php
// login.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | EchoMind</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include "layout/header.php"; ?>

<main class="auth-page">
    <div class="auth-container">
        <div class="auth-card">

            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">
                Log in to access your EchoMind space.
            </p>

            <!-- Error / Success Messages -->
            <?php if(isset($_GET['error'])): ?>
                <div class="auth-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <?php if(isset($_GET['success'])): ?>
                <div class="auth-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <form action="backend/auth/login.php" method="POST">

                <label>Email Address</label>
                <input type="email" name="email" required placeholder="you@example.com">

                <label>Password</label>
                <input type="password" name="password" required placeholder="Your password">

                <button type="submit" class="btn auth-btn">Login</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Register</a>
            </div>

        </div>
    </div>
</main>

<?php include "layout/footer.php"; ?>

</body>
</html>
