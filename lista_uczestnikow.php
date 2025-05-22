<?php
require_once "login/auth.php";

$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}
$uczestnicy = $conn->query("SELECT * FROM uczestnicy ORDER BY nazwisko, imie");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Lista Uczestników</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            padding: 40px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>📋 Lista uczestników konkursu</h2>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Imię</th>
        <th>Nazwisko</th>
        <th>Klasa</th>
        <th>Szkoła</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($uczestnicy->num_rows > 0): ?>
        <?php while ($u = $uczestnicy->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['imie']) ?></td>
                <td><?= htmlspecialchars($u['nazwisko']) ?></td>
                <td><?= htmlspecialchars($u['klasa']) ?></td>
                <td><?= htmlspecialchars($u['szkola']) ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">Brak uczestników w bazie.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<a href="panel.php" class="back-link">⬅ Powrót do panelu</a>

</body>
</html>
