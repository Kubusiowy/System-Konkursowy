<?php
require_once "login/auth.php";


$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("Błąd DB");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nazwa_kategorii = $_POST['nazwa'];
    $kryteria = isset($_POST['kryteria']) ? $_POST['kryteria'] : [];
    $maks = isset($_POST['maks']) ? $_POST['maks'] : [];

    if (!empty($nazwa_kategorii) && count($kryteria) > 0) {
        $stmt = $conn->prepare("INSERT INTO kategorie (nazwa) VALUES (?)");
        $stmt->bind_param("s", $nazwa_kategorii);
        $stmt->execute();
        $kategoria_id = $stmt->insert_id;
        $stmt->close();

        $stmt_k = $conn->prepare("INSERT INTO kryteria (kategoria_id, nazwa, maks_punkty) VALUES (?, ?, ?)");
        foreach ($kryteria as $index => $nazwa_kryt) {
            $nazwa_kryt = trim($nazwa_kryt);
            $max = (int)(isset($maks[$index]) ? $maks[$index] : 10);
            if ($nazwa_kryt !== '') {
                $stmt_k->bind_param("isi", $kategoria_id, $nazwa_kryt, $max);
                $stmt_k->execute();
            }
        }
        $stmt_k->close();

        $sukces = "Dodano kategorię i kryteria z punktami maksymalnymi!";
    } else {
        $blad = "Wprowadź nazwę kategorii i przynajmniej jedno kryterium.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dodaj kategorię z kryteriami</title>
    <style>
        body { font-family: Arial; background: #f1f1f1; padding: 40px; text-align: center; }
        .form-box { background: white; padding: 30px; border-radius: 15px; display: inline-block; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 500px; }
        input[type="text"], input[type="number"] {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            font-size: 16px;
        }
        .kryteria-group { margin-top: 15px; }
        .kryteria-row { display: flex; justify-content: space-between; gap: 10px; }
        button, input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
        }
        button { background: #007BFF; color: white; }
        button:hover { background: #0056b3; }
        input[type="submit"] { background: #28a745; color: white; }
        input[type="submit"]:hover { background: #218838; }
        .msg { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Dodaj kategorię i kryteria</h2>
    <form method="POST" id="form">
        <input type="text" name="nazwa" placeholder="Nazwa kategorii" required><br>

        <div class="kryteria-group" id="kryteria">
            <div class="kryteria-row">
                <input type="text" name="kryteria[]" placeholder="Kryterium 1" required>
                <input type="number" name="maks[]" placeholder="Maks. pkt" min="1" value="10" required>
            </div>
        </div>

        <button type="button" onclick="dodajKryterium()">➕ Dodaj kolejne kryterium</button><br>
        <input type="submit" value="Zapisz kategorię">
    </form>

    <?php if (!empty($sukces)) echo "<div class='msg' style='color:green;'>$sukces</div>"; ?>
    <?php if (!empty($blad)) echo "<div class='msg' style='color:red;'>$blad</div>"; ?>
    <a href="panel.php" style="display:block; text-align:center; margin-top: 30px; ">⬅ Powrót do panelu</a>
</div>

<script>
    let licznik = 2;
    function dodajKryterium() {
        const div = document.getElementById("kryteria");
        const row = document.createElement("div");
        row.className = "kryteria-row";

        const inputNazwa = document.createElement("input");
        inputNazwa.type = "text";
        inputNazwa.name = "kryteria[]";
        inputNazwa.placeholder = "Kryterium " + licznik;

        const inputMaks = document.createElement("input");
        inputMaks.type = "number";
        inputMaks.name = "maks[]";
        inputMaks.placeholder = "Maks. pkt";
        inputMaks.min = 1;
        inputMaks.value = 10;

        row.appendChild(inputNazwa);
        row.appendChild(inputMaks);
        div.appendChild(row);
        licznik++;
    }
</script>

</body>
</html>
