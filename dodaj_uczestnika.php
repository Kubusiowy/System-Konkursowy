<?php
require_once "login/auth.php";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dodaj Uczestnika</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-box {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 30px;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            width: 250px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        a {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #28a745;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Dodaj Uczestnika</h2>
    <form method="POST">
        <input type="text" name="imie" placeholder="Imię" required><br>
        <input type="text" name="nazwisko" placeholder="Nazwisko" required><br>
        <input type="submit" value="Dodaj">
    </form>
    <a href="panel.php">⬅ Powrót do panelu</a>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = new mysqli("localhost", "root", "", "konkurs");
        if ($conn->connect_error) {
            die("Błąd połączenia: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO uczestnicy (imie, nazwisko) VALUES (?, ?)");
        $stmt->bind_param("ss", $_POST['imie'], $_POST['nazwisko']);
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Uczestnik dodany pomyślnie!</p>";
        } else {
            echo "<p style='color:red;'>Błąd: " . $stmt->error . "</p>";
        }
        $stmt->close();
        $conn->close();
    }
    ?>
</div>

</body>
</html>
