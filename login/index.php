<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "konkurs");
    if ($conn->connect_error) {
        die("Błąd połączenia z bazą danych: " . $conn->connect_error);
    }

    $login = $_POST['login'];
    $haslo = $_POST['haslo'];

    $sql = "SELECT * FROM admin WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($haslo, $row['haslo'])) {
            $_SESSION['admin'] = $row['login'];
            header("Location: ../panel.php");
            exit;
        } else {
            $error = "❌ Nieprawidłowe hasło.";
        }
    } else {
        $error = "❌ Nie znaleziono użytkownika.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .login-box button {
            width: 100%;
            padding: 14px;
            background-color: #007BFF;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .login-box button:hover {
            background-color: #0056b3;
        }

        .error {
            background-color: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

    </style>
</head>
<body>
<div class="login-box">
    <h2>🔐 Panel logowania</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="login" placeholder="Login" required>
        <input type="password" name="haslo" placeholder="Hasło" required>
        <button type="submit">Zaloguj się</button>
    </form>
</div>
</body>
</html>
