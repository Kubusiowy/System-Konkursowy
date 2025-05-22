<?php
require_once "login/auth.php";

$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("Błąd połączenia z bazą");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])) {
    $conn->autocommit(false);

    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("DELETE FROM oceny");
    $conn->query("DELETE FROM uczestnicy");
    $conn->query("DELETE FROM jury");
    $conn->query("DELETE FROM kryteria");
    $conn->query("DELETE FROM kategorie");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    $conn->commit();
    $conn->autocommit(true);

    $resetMsg = "🧹 Baza danych została wyczyszczona!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel Konkursowy</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f1f1;
        }

        .navbar {
            background-color: #007BFF;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background-color: #a71d2a;
        }

        .container {
            padding: 40px;
            max-width: 1200px;
            margin: auto;
        }

        .btn {
            display: block;
            margin: 15px auto;
            padding: 15px 30px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .reset-btn {
            background-color: #dc3545;
        }

        .reset-btn:hover {
            background-color: #a71d2a;
        }

        .msg {
            margin-top: 20px;
            font-weight: bold;
            color: green;
            text-align: center;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            justify-items: center;
            max-width: 700px;
            margin: 0 auto 40px auto;
        }

    </style>
    <script>
        function potwierdzReset() {
            return confirm("⚠️ Czy na pewno chcesz zresetować całą bazę danych? Tej operacji NIE można cofnąć!");
        }
    </script>
</head>
<body>

<div class="navbar">
    <h1>🛎️ Konkurs Hotelarski – Panel</h1>
    <a href="login/logout.php" class="logout-btn">🚪 Wyloguj się</a>
</div>

<div class="container">

    <div class="grid">
        <a href="dodaj_uczestnika.php" class="btn">➕ Dodaj Uczestnika</a>
        <a href="dodaj_jurora.php" class="btn">➕ Dodaj Jurora</a>
        <a href="dodaj_kategorie.php" class="btn">➕ Dodaj Kategorię</a>
        <a href="kategorie_lista.php" class="btn">📋 Kategorie i Kryteria</a>
        <a href="ocena.php" class="btn">📝 Dodaj Ocenę</a>
        <a href="lista_uczestnikow.php" class="btn">👥Lista Uczestników</a>
        <a href="wyniki.php" class="btn">📊 Zobacz Wyniki</a>
        <a href="wyniki_szczegolowe.php" class="btn">📊 Wyniki Szczegółowe</a>

        <?php if (isset($resetMsg)) echo "<div class='msg'>$resetMsg</div>"; ?>


    </div>
    <form method="POST" onsubmit="return potwierdzReset();">
        <input type="submit" name="reset" value="🧹 Zresetuj bazę danych" class="btn reset-btn">
    </form>
</div>
<footer style="
    background-color: #007BFF;
    color: white;
    text-align: center;
    padding: 15px;
    font-size: 14px;
    margin-top: 40px;
    border-top: 2px solid #0056b3;">
    © 2025 Jakub Firkowski – Konkurs Hotelarski | 22 maja 2025
</footer>

</body>
</html>
