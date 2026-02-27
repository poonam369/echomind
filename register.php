<?php
// register.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | EchoMind</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include "layout/header.php"; ?>

<main class="auth-page">
    <div class="auth-container">
        <div class="auth-card">

            <h2 class="auth-title">Create your EchoMind account</h2>
            <p class="auth-subtitle">
                A calm space for reflection, habits, and clarity.
            </p>

            <?php if(isset($_GET['error'])): ?>
                <div class="auth-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <?php if(isset($_GET['success'])): ?>
                <div class="auth-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <form action="backend/auth/register.php" method="POST">

                <label>Full Name</label>
                <input type="text" name="fullname" required placeholder="Your name">

                <label>Email Address</label>
                <input type="email" name="email" required placeholder="you@example.com">

                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a password">

                <button type="submit" class="btn auth-btn">Create Account</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Login</a>
            </div>

        </div>
    </div>
</main>

<?php include "layout/footer.php"; ?>

</body>
</html>
