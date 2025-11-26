<?php
session_start();
require_once __DIR__ . '/../config.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($user === '' || $password === '') {
        $error = 'Please enter your username (or email) and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE username = :u OR email = :u LIMIT 1');
        $stmt->execute([':u' => $user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($password, $row['password'])) {
            // Successful login
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header('Location: ../pages/ui.html');
            exit;
        } else {
            $error = 'Invalid username/email or password.';
        }
    }
}
// Show success message when redirected after registration
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
  $success = 'Registration successful. Please sign in.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CineRate - Sign In</title>
  <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body>

  <!-- Top Banner -->
  <div class="top-banner">
    <h2>Sign in to <span>CineRate</span></h2>
    <p>Rate a movie, unlock your next favorite.</p>
  </div>

  <!-- Main Container -->
  <div class="signin-container">
    <!-- Left Poster Side -->
    <div class="left-side">
      <img src="../assets/image/loginbg.png" alt="Movie Collage">
      <div class="logo-text"></div>
    </div>

    <!-- Right Form Side -->
    <div class="right-side">
        <div class="back-button">
      <a href="../index.html" class="back-btn"> ⬅ BACK</a>
      </div>
      
      <h2>Welcome Back!</h2>
      <h3>Sign In</h3>

      <?php if ($success): ?>
        <div class="alert success" role="status"><?=htmlspecialchars($success)?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert error" role="alert"><?=htmlspecialchars($error)?></div>
      <?php endif; ?>

      <form class="signin-form" method="post" action="">
        <label for="username">Username*</label>
        <input id="username" name="username" type="text" placeholder="Username or email" required value="<?=htmlspecialchars($_POST['username'] ?? '')?>">

        <label for="password">Password*</label>
        <input id="password" name="password" type="password" placeholder="Password" required>

        <div class="options">
          <label><input type="checkbox" name="remember"> Remember Me</label>
          <a href="forgot-password.html">Forgot password?</a>
        </div>

        <button type="submit" class="signin-btn">SIGN IN</button>

        <p class="signup-text">
          Don't have an account? <a href="register.php">Create One</a>
        </p>

        <div class="divider">
          <span>Sign in with</span>
        </div>

        <div class="social-buttons">
          <button class="google" type="button">G</button>
          <button class="facebook" type="button">f</button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
