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
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #1c1c1c;
            color: #f0f0f0;
            padding: 40px;
            text-align: center;
        }

        .table-wrapper {
            overflow-x: auto;
            max-width: 100%;
            border: 1px solid #444;
            padding-bottom: 10px;
            scroll-behavior: auto;

        }

        table {
            border-collapse: collapse;
            margin: 0 auto;
            min-width: 2400px;
            width: max-content;
            table-layout: auto;
            background: #1e1e1e;
            color: #f1f1f1;
        }

        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            word-break: break-word;
            white-space: normal;
        }

        thead th {
            background-color: #333;
            color: #fff;
        }

        thead tr:nth-child(1) th { background-color: #444; }
        thead tr:nth-child(2) th { background-color: #3a3a3a; }
        thead tr:nth-child(3) th { background-color: #2a2a2a; }

        tr:nth-child(even) { background: #2a2a2a; }
        tr:nth-child(odd) { background: #1e1e1e; }

        td:not(:empty):not(.total) {
            background-color: #004b93;
            color: #fff;
            font-weight: bold;
        }

        td:first-child {
            font-weight: bold;
            text-align: left;
            white-space: nowrap;
            background-color: #252525;
        }

        .total {
            background: #0073e6;
            font-weight: bold;
            color: #fff;
            position: sticky;
            right: 0;
            z-index: 1;
        }

        h1 {
            margin-bottom: 20px;
            color: #ffffff;
        }

        .auto-refresh {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #444;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #fff;
            z-index: 5;
        }
    </style>

</head>
<body>

<div class="auto-refresh">
    ⏳ Odświeżanie: <span id="status">Włączone</span><br>
    <button onclick="toggleRefresh()">🔁 Przełącz</button>
</div>
<div class="table-wrapper">
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
                        $style = $val !== '' ? 'style="background:#004b93; color:#fff; font-weight:bold;"' : '';
                        ?>
                        <td <?= $style ?>><?= $val !== '' ? $val : '-' ?></td>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <td class="total"><?= $suma ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<a href="panel.php" style="display:block; text-align:center; margin-top: 30px; ">⬅ Powrót do panelu</a>

<script>
    let refreshEnabled = sessionStorage.getItem('refresh') !== 'false'; // domyślnie true

    const wrapper = document.querySelector('.table-wrapper');


    window.addEventListener('load', () => {
        const savedScroll = sessionStorage.getItem('scrollX');
        if (savedScroll && wrapper) {
            wrapper.scrollLeft = parseInt(savedScroll);
        }


        if (refreshEnabled) {
            setTimeout(() => {
                if (wrapper) {
                    sessionStorage.setItem('scrollX', wrapper.scrollLeft);
                }
                location.reload();
            }, 2500);
        }


        document.getElementById('status').textContent = refreshEnabled ? 'Włączone' : 'Wyłączone';
    });


    function toggleRefresh() {
        refreshEnabled = !refreshEnabled;
        sessionStorage.setItem('refresh', refreshEnabled);
        document.getElementById('status').textContent = refreshEnabled ? 'Włączone' : 'Wyłączone';
    }
</script>

</body>
</html>