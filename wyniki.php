<?php
require_once "login/auth.php";


$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("Błąd bazy danych");

// słowniki
$uczestnicy = $conn->query("SELECT * FROM uczestnicy ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$jury = $conn->query("SELECT * FROM jury ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$kategorie = $conn->query("SELECT * FROM kategorie ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$kryteria = $conn->query("SELECT * FROM kryteria ORDER BY kategoria_id, id")->fetch_all(MYSQLI_ASSOC);

// mapki
$uczMap = []; foreach ($uczestnicy as $u) $uczMap[$u['id']] = $u['imie'] . ' ' . $u['nazwisko'];
$juryMap = []; foreach ($jury as $j) $juryMap[$j['id']] = $j['imie'] . ' ' . $j['nazwisko'];
$katMap = []; foreach ($kategorie as $k) $katMap[$k['id']] = $k['nazwa'];

$kryteriaByKat = [];
foreach ($kryteria as $k) $kryteriaByKat[$k['kategoria_id']][] = $k;

// dane ucznia
$uczId = $_GET['uczestnik_id'] ?? null;
$oceny = [];
if ($uczId) {
    $stmt = $conn->prepare("SELECT * FROM oceny WHERE uczestnik_id = ?");
    $stmt->bind_param("i", $uczId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($res as $o) {
        $oceny[$o['kryterium_id']][$o['juror_id']] = $o['punkty'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wyniki poziome ucznia</title>
    <style>
        body { font-family:Arial; background:#121212; color:#f1f1f1; padding:40px; text-align:center; }
        table { border-collapse:collapse; width:95%; margin:20px auto; background:#1e1e1e; }
        th, td { border:1px solid #333; padding:8px 12px; text-align:center; }
        th { background:#333; }
        tfoot td { font-weight:bold; background:#004b93; color:#fff; }
        tr:nth-child(even) { background:#2a2a2a; }
        select { padding:10px; font-size:16px; border-radius:6px; background:#000; color:white; border:1px solid #555; }
        input[type=submit] {
            padding:10px 20px; font-size:16px; border-radius:6px; background:#007bff; color:white; border:none;
            margin-top:10px;
        }
        input[type=submit]:hover { background:#1484ff; cursor:pointer; }
        h2 { margin-top:40px; }
    </style>
</head>
<body>

<h1>📋 Szczegółowe wyniki ucznia (poziomo)</h1>

<form method="GET">
    <label>Wybierz ucznia:
        <select name="uczestnik_id" required>
            <option value="">— wybierz —</option>
            <?php foreach ($uczestnicy as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $u['id'] == $uczId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['imie'] . ' ' . $u['nazwisko']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <br>
    <input type="submit" value="🔍 Pokaż wyniki">
</form>

<?php if ($uczId): ?>
    <h2>Uczeń: <?= htmlspecialchars($uczMap[$uczId]) ?></h2>

    <table>
        <thead>
        <tr>
            <th>Kategoria</th>
            <th>Kryterium</th>
            <th>Max</th>
            <?php foreach ($jury as $j): ?>
                <th><?= htmlspecialchars($j['imie']) ?><br><?= htmlspecialchars($j['nazwisko']) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php
        $sumaJurora = [];
        foreach ($kategorie as $kat):
            foreach ($kryteriaByKat[$kat['id']] ?? [] as $k):
                ?>
                <tr>
                    <td><?= htmlspecialchars($kat['nazwa']) ?></td>
                    <td><?= htmlspecialchars($k['nazwa']) ?></td>
                    <td><?= $k['maks_punkty'] ?></td>
                    <?php foreach ($jury as $j):
                        $pkt = $oceny[$k['id']][$j['id']] ?? null;
                        if (!isset($sumaJurora[$j['id']])) $sumaJurora[$j['id']] = 0;
                        if ($pkt !== null) $sumaJurora[$j['id']] += (int)$pkt;
                        ?>
                        <td><?= $pkt !== null ? (int)$pkt : '-' ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach;
        endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3">Suma punktów od jurora</td>
            <?php foreach ($jury as $j): ?>
                <td><?= $sumaJurora[$j['id']] ?? 0 ?></td>
            <?php endforeach; ?>
        </tr>
        </tfoot>
    </table>
<?php endif; ?>
<a href="panel.php" style="display:block; text-align:center; margin-top: 30px; text-decoration: none;">⬅ Powrót do panelu</a>
</body>
</html>
