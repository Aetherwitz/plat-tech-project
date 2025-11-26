<?php
// Register page with server-side handling
session_start();
require_once __DIR__ . '/../config.php';

$errors = [];
$values = ['email' => '', 'username' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $values['email'] = $email;
    $values['username'] = $username;

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($username === '' || strlen($username) < 3) {
        $errors[] = 'Please choose a username (at least 3 characters).';
    }
    if ($password === '' || strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        // Check duplicates
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e OR username = :u LIMIT 1');
        $stmt->execute([':e' => $email, ':u' => $username]);
        if ($stmt->fetch()) {
            $errors[] = 'A user with that email or username already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:u, :e, :p)');
            $ins->execute([':u' => $username, ':e' => $email, ':p' => $hash]);
            // Redirect to login with success param
            header('Location: ../pages/login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>CineRate - Register</title>
  <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body>

  <div class="top-banner">
    <h2>Join <span>CineRate</span></h2>
    <p>Rate a movie, unlock your next favorite.</p>
  </div>

  <div class="signin-container">
    <div class="left-side">
      <img src="../assets/image/loginbg.png" alt="Movie Collage">
      <div class="logo-text">CINERATE</div>
    </div>

    <div class="right-side">
      <div class="form-header">
        <a href="../pages/login.php" class="back-btn">⬅ BACK</a>
        <h2>Register</h2>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert error" role="alert">
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?=htmlspecialchars($e)?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="signin-form" method="post" action="" novalidate>
        <label for="email">Email *</label>
        <input id="email" name="email" type="email" placeholder="Email" required value="<?=htmlspecialchars($values['email'])?>">

        <label for="username">Username *</label>
        <input id="username" name="username" type="text" placeholder="Username" required value="<?=htmlspecialchars($values['username'])?>">

        <label for="password">Password *</label>
        <input id="password" name="password" type="password" placeholder="Password" required>

        <label for="password_confirm">Confirm Password *</label>
        <input id="password_confirm" name="password_confirm" type="password" placeholder="Confirm password" required>

        <label>Birthdate *</label>
        <div class="birthdate">
          <select id="month" name="month" aria-label="Month">
            <option value="" disabled selected>Month</option>
          </select>

          <select id="day" name="day" aria-label="Day">
            <option value="" disabled selected>Day</option>
          </select>

          <select id="year" name="year" aria-label="Year">
            <option value="" disabled selected>Year</option>
          </select>
        </div>
        
        <script src="../js/birthdate.js" defer></script>

        <label class="terms">
          <input type="checkbox" required>
          By continuing, you agree to CineRate Terms of Use and Privacy Policy.
        </label>

        <button type="submit" class="signin-btn">CREATE ACCOUNT</button>

        <p class="signup-text">Already have an account? <a href="../pages/login.php">Sign in</a></p>

        <div class="divider">Sign up with</div>

        <div class="social-buttons">
          <button class="google" aria-label="Sign up with Google" type="button">G</button>
          <button class="facebook" aria-label="Sign up with Facebook" type="button">f</button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>