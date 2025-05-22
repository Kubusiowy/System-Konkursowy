<?php
require_once "login/auth.php";


$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("Błąd bazy danych");

$uczestnicy = $conn->query("SELECT * FROM uczestnicy ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$jury       = $conn->query("SELECT * FROM jury ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$kategorie  = $conn->query("SELECT * FROM kategorie ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$kryteria   = $conn->query("SELECT * FROM kryteria ORDER BY kategoria_id, id")->fetch_all(MYSQLI_ASSOC);
$oceny      = $conn->query("SELECT * FROM oceny")->fetch_all(MYSQLI_ASSOC);


$juryMap = array_column($jury, null, 'id');
$krytMap = array_column($kryteria, null, 'id');
$katMap  = array_column($kategorie, 'nazwa', 'id');


$kryteriaByKat = [];
foreach ($kryteria as $k) $kryteriaByKat[$k['kategoria_id']][] = $k;


$punkty = [];
foreach ($oceny as $o) {
    $punkty[$o['uczestnik_id']][$o['kryterium_id']][$o['juror_id']] = $o['punkty'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Tablica ocen LIVE</title>
    <style>
        body { font-family:Arial; background:#121212; color:#eee; padding:40px; text-align:center; }
        table { border-collapse:collapse; margin:0 auto; background:#1e1e1e; color:#f1f1f1; }
        th, td { border:1px solid #333; padding:6px 10px; text-align:center; font-size:14px; }
        th { background:#333; position:sticky; top:0; z-index:3; }
        tr:nth-child(even) { background:#2a2a2a; }
        .total { background:#004b93; font-weight:bold; color:#fff; position:sticky; right:0; }
        .auto-refresh { position:fixed; top:10px; right:10px; background:#444; padding:6px 12px; border-radius:8px; font-size:13px; color:#fff }
    </style>
    <script>
        setTimeout(() => location.reload(), 10000);
    </script>
</head>
<body>

<h1>🧾 Tablica ocen – LIVE</h1>
<div class="auto-refresh">⏳ Odświeżanie co 10s</div>

<table>
    <thead>
    <tr>
        <th rowspan="3">Uczestnik</th>
        <?php foreach ($kategorie as $kat):
            $colspan = 0;
            foreach ($kryteriaByKat[$kat['id']] ?? [] as $k) $colspan += count($jury);
            ?>
            <th colspan="<?= $colspan ?>"><?= htmlspecialchars($kat['nazwa']) ?></th>
        <?php endforeach; ?>
        <th rowspan="3" class="total">Suma</th>
    </tr>
    <tr>
        <?php foreach ($kategorie as $kat): ?>
            <?php foreach ($kryteriaByKat[$kat['id']] ?? [] as $k): ?>
                <th colspan="<?= count($jury) ?>"><?= htmlspecialchars($k['nazwa']) ?><br>(<?= $k['maks_punkty'] ?>)</th>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($kategorie as $kat): ?>
            <?php foreach ($kryteriaByKat[$kat['id']] ?? [] as $k): ?>
                <?php foreach ($jury as $j): ?>
                    <th><?= htmlspecialchars($j['imie']) ?></th>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($uczestnicy as $u): ?>
        <tr>
            <td><strong><?= htmlspecialchars($u['imie'] . ' ' . $u['nazwisko']) ?></strong></td>
            <?php $suma = 0; ?>
            <?php foreach ($kategorie as $kat): ?>
                <?php foreach ($kryteriaByKat[$kat['id']] ?? [] as $k): ?>
                    <?php foreach ($jury as $j): ?>
                        <?php
                        $val = $punkty[$u['id']][$k['id']][$j['id']] ?? '';
                        $suma += is_numeric($val) ? (int)$val : 0;
                        ?>
                        <td><?= $val !== '' ? $val : '-' ?></td>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <td class="total"><?= $suma ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<a href="panel.php" style="display:block; text-align:center; margin-top: 30px; ">⬅ Powrót do panelu</a>
</body>
</html>
