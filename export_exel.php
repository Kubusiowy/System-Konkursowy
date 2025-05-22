<?php

require_once "login/auth.php";


$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("Błąd połączenia z bazą");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=wyniki_szczegolowe.xls");

$uczestnicy = $conn->query("SELECT * FROM uczestnicy ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$jury = $conn->query("SELECT * FROM jury ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$kategorie = $conn->query("SELECT * FROM kategorie ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$kryteria = $conn->query("SELECT * FROM kryteria ORDER BY kategoria_id, id")->fetch_all(MYSQLI_ASSOC);
$oceny = $conn->query("SELECT * FROM oceny")->fetch_all(MYSQLI_ASSOC);

$kryteriaMap = [];
foreach ($kryteria as $k) {
    $kryteriaMap[$k['id']] = $k;
}

$kategorieMap = [];
foreach ($kategorie as $kat) {
    $kategorieMap[$kat['id']] = $kat['nazwa'];
}

$juryMap = [];
foreach ($jury as $j) {
    $juryMap[$j['id']] = $j['imie'] . ' ' . $j['nazwisko'];
}

$uczestnicyMap = [];
foreach ($uczestnicy as $u) {
    $uczestnicyMap[$u['id']] = $u['imie'] . ' ' . $u['nazwisko'];
}

echo "<table border='1'>";
echo "<tr><th>Uczestnik</th><th>Kategoria</th><th>Kryterium</th><th>Maks. Punkty</th><th>Juror</th><th>Punkty</th></tr>";

foreach ($oceny as $o) {
    $ucz = htmlspecialchars(isset($uczestnicyMap[$o['uczestnik_id']]) ? $uczestnicyMap[$o['uczestnik_id']] : 'Nieznany');
    $kryt = isset($kryteriaMap[$o['kryterium_id']]) ? $kryteriaMap[$o['kryterium_id']] : null;
    $katNazwa = htmlspecialchars(isset($kategorieMap[$kryt['kategoria_id']]) ? $kategorieMap[$kryt['kategoria_id']] : '');
    $krytNazwa = htmlspecialchars(isset($kryt['nazwa']) ? $kryt['nazwa'] : '');
    $maks = isset($kryt['maks_punkty']) ? $kryt['maks_punkty'] : '';
    $juror = htmlspecialchars(isset($juryMap[$o['juror_id']]) ? $juryMap[$o['juror_id']] : '');
    $punkty = isset($o['punkty']) ? (int)$o['punkty'] : 0;

    echo "<tr>
        <td>$ucz</td>
        <td>$katNazwa</td>
        <td>$krytNazwa</td>
        <td>$maks</td>
        <td>$juror</td>
        <td>$punkty</td>
    </tr>";
}

echo "</table>";
$conn->close();
?>
