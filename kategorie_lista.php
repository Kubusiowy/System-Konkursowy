<?php

require_once "login/auth.php";


$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("Błąd połączenia z bazą");

// USUWANIE kategorii
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM kategorie WHERE id = $id");
    header("Location: kategorie_lista.php");
    exit;
}

// USUWANIE kryterium
if (isset($_GET['delete_kryt']) && is_numeric($_GET['delete_kryt'])) {
    $id = (int)$_GET['delete_kryt'];
    $conn->query("DELETE FROM kryteria WHERE id = $id");
    header("Location: kategorie_lista.php");
    exit;
}

// EDYCJA kategorii
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $id = (int)$_POST['edit_id'];
    $nowa_nazwa = trim($_POST['nowa_nazwa']);
    if ($nowa_nazwa !== '') {
        $stmt = $conn->prepare("UPDATE kategorie SET nazwa=? WHERE id=?");
        $stmt->bind_param("si", $nowa_nazwa, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: kategorie_lista.php");
        exit;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_kryt_id'])) {
    $id = (int)$_POST['edit_kryt_id'];
    $nowa_nazwa = trim($_POST['kryt_nazwa']);
    $max_punkty = (int)$_POST['kryt_maks'];
    if ($nowa_nazwa !== '' && $max_punkty > 0) {
        $stmt = $conn->prepare("UPDATE kryteria SET nazwa=?, maks_punkty=? WHERE id=?");
        $stmt->bind_param("sii", $nowa_nazwa, $max_punkty, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: kategorie_lista.php");
        exit;
    }
}

$kategorie = $conn->query("SELECT * FROM kategorie ORDER BY nazwa")->fetch_all(MYSQLI_ASSOC);
$kryteria = $conn->query("SELECT * FROM kryteria ORDER BY kategoria_id, id")->fetch_all(MYSQLI_ASSOC);
$kryteriaMap = [];
foreach ($kryteria as $k) {
    $kryteriaMap[$k['kategoria_id']][] = $k;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kategorie i kryteria</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #181818;
            color: #f1f1f1;
            padding: 40px;
        }
        h1 {
            text-align: center;
            margin-bottom: 40px;
        }
        .kategoria {
            background: #242424;
            margin: 20px auto;
            padding: 25px;
            max-width: 700px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.4);
        }
        .kategoria h2 {
            margin: 0 0 10px;
            color: #00aaff;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            background: #303030;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 8px;
            text-align: left;
            font-size: 16px;
        }
        .empty {
            color: gray;
            font-style: italic;
        }
        a, button {
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            background: #ff4444;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 5px;
        }
        form.inline {
            display: inline;
        }
        input[type="text"], input[type="number"] {
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #555;
            margin-bottom: 5px;
            width: 45%;
        }
    </style>
</head>
<body>

<h1>🛠️ Edycja kategorii i kryteriów</h1>

<?php foreach ($kategorie as $kat): ?>
    <div class="kategoria">
        <form method="POST" class="inline">
            <input type="hidden" name="edit_id" value="<?= $kat['id'] ?>">
            <input type="text" name="nowa_nazwa" value="<?= htmlspecialchars($kat['nazwa']) ?>">
            <button type="submit">💾 Zapisz</button>
        </form>
        <a href="?delete=<?= $kat['id'] ?>" onclick="return confirm('Usunąć tę kategorię?')">🗑️ Usuń</a>

        <h2><?= htmlspecialchars($kat['nazwa']) ?></h2>
        <?php if (!empty($kryteriaMap[$kat['id']])): ?>
            <ul>
                <?php foreach ($kryteriaMap[$kat['id']] as $kryt): ?>
                    <li>
                        <form method="POST" class="inline">
                            <input type="hidden" name="edit_kryt_id" value="<?= $kryt['id'] ?>">
                            <input type="text" name="kryt_nazwa" value="<?= htmlspecialchars($kryt['nazwa']) ?>">
                            <input type="number" name="kryt_maks" value="<?= $kryt['maks_punkty'] ?>" min="1">
                            <button type="submit">💾</button>
                        </form>
                        <a href="?delete_kryt=<?= $kryt['id'] ?>" onclick="return confirm('Usunąć to kryterium?')">🗑️</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="empty">Brak kryteriów</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<a href="panel.php" style="display:block; text-align:center; margin-top: 30px; ">⬅ Powrót do panelu</a>

</body>
</html>