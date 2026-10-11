<?php
require __DIR__ . '/db.php';
session_start();

if (isset($_GET['status'])) {
    header('Content-Type: text/plain');
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        exit;
    }
    echo $_SESSION['name'];
    exit;
}

$error = '';
$old = ['name' => '', 'username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $loginError = '';

        if ($username === '' || $password === '') {
            $loginError = 'All fields are required.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM xss WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $loginError = 'Invalid username or password.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                header('Location: index.html');
                exit;
            }
        }

        header('Location: index.html?error=' . urlencode($loginError));
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $old = ['name' => $name, 'username' => $username, 'email' => $email];

    if ($name === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $check = $pdo->prepare('SELECT id FROM xss WHERE username = ? OR email = ?');
        $check->execute([$username, $email]);

        if ($check->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            $insert = $pdo->prepare('INSERT INTO xss (name, username, email, password) VALUES (?, ?, ?, ?)');
            $insert->execute([$name, $username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: index.html?registered=1');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
  <form method="post" action="register.php" class="auth-form">
    <h1>Register</h1>

    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name']) ?>" required>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" value="<?= htmlspecialchars($old['username']) ?>" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <label for="confirm_password">Confirm Password</label>
    <input type="password" id="confirm_password" name="confirm_password" required>

    <button type="submit">Register</button>

    <?php if ($error): ?>
      <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p class="switch">Already have an account? <a href="index.html">Log in</a></p>
  </form>
</body>
</html>