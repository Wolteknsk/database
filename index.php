<?php

$conn = new mysqli('localhost', 'root', '', 'simple_auth');

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($check->num_rows > 0) {
        $reg_error = "Этот email уже зарегистрирован!";
    } else {
        $conn->query("INSERT INTO users (email, password) VALUES ('$email', '$password')");
        $reg_success = "Регистрация успешна!";
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
    } else {
        $login_error = "Неверный email или пароль!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Простой сайт</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 50px auto; padding: 20px; }
        input, button { width: 100%; padding: 10px; margin: 5px 0; }
        .error { color: red; }
        .success { color: green; }
        .info { background: #f0f0f0; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <h2>Привет, <?php echo $_SESSION['user_email']; ?>!</h2>
    <div class="info">
        <p><strong>Email:</strong> <?php echo $_SESSION['user_email']; ?></p>
        <p><strong>ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
    </div>
    <a href="?logout=1"><button>Выйти</button></a>

<?php else: ?>
    <h2>Авторизация</h2>
    <?php if (isset($login_error)) echo "<p class='error'>$login_error</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit" name="login">Войти</button>
    </form>

    <h2>Регистрация</h2>
    <?php if (isset($reg_error)) echo "<p class='error'>$reg_error</p>"; ?>
    <?php if (isset($reg_success)) echo "<p class='success'>$reg_success</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit" name="register">Зарегистрироваться</button>
    </form>
<?php endif; ?>

</body>
</html>