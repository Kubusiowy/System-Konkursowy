<?php
require_once "login/auth.php";


$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("💥 Błąd bazy");

// słowniki
$uczestnicy = $conn->query("SELECT id, CONCAT(imie,' ',nazwisko) AS n FROM uczestnicy ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$jury       = $conn->query("SELECT id, CONCAT(imie,' ',nazwisko) AS n FROM jury ORDER BY nazwisko, imie")->fetch_all(MYSQLI_ASSOC);
$kategorie  = $conn->query("SELECT id, nazwa FROM kategorie ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$kryteria   = $conn->query("SELECT * FROM kryteria ORDER BY kategoria_id, id")->fetch_all(MYSQLI_ASSOC);

// grupujemy kryteria wg kategorii
$byCat = [];
foreach($kryteria as $k) $byCat[$k['kategoria_id']][] = $k;

// istniejące oceny
$oceny = [];
$res = $conn->query("SELECT * FROM oceny");
foreach($res->fetch_all(MYSQLI_ASSOC) as $o)
    $oceny[$o['uczestnik_id']][$o['kryterium_id']][$o['juror_id']] = $o['punkty'];

// zapis
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ucz'], $_POST['jur'], $_POST['kat'])) {
    $ucz = (int)$_POST['ucz'];
    $jur = (int)$_POST['jur'];
    $kat = (int)$_POST['kat'];

    $conn->begin_transaction();
    $stmt = $conn->prepare("INSERT INTO oceny (uczestnik_id, juror_id, kryterium_id, punkty) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE punkty = VALUES(punkty)");

    foreach ($_POST['punkty'] ?? [] as $kryId => $val) {
        $maks = (int)$_POST['maks'][$kryId];
        $v = max(0, min((int)$val, $maks));
        $stmt->bind_param("iiii", $ucz, $jur, $kryId, $v);
        if (!$stmt->execute()) {
            $conn->rollback();
            $msg = "❌ Błąd: " . $stmt->error;
            goto bye;
        }
    }
    $conn->commit();
    $msg = "✅ Oceny zapisane!";
}
bye:
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Oceny – jedna strona</title>
    <style>
        body { font-family:Arial;background:#121212;color:#eee;padding:40px;text-align:center }
        select, input { padding:6px 10px;border-radius:6px;border:1px solid #555;background:#000;color:#fff;font-size:16px }
        form { display:inline-block;background:#1e1e1e;padding:30px 40px;border-radius:12px }
        label { display:block;margin:15px 0 }
        table { border-collapse:collapse;margin:25px auto }
        th, td { border:1px solid #444;padding:6px 10px }
        th { background:#333 }
        button { margin-top:25px;padding:10px 25px;font-size:18px;border-radius:8px;border:1px solid #555;
            background:#007bff;color:#fff;cursor:pointer }
        button:hover { background:#1484ff }
        p.msg { font-size:18px;font-weight:bold }
    </style>
</head>
<body>

<h1>🧑‍⚖️ Dodawanie ocen</h1>
<?php if ($msg): ?><p class="msg"><?= $msg ?></p><?php endif; ?>

<form method="POST">
    <label>Uczeń:
        <select name="ucz" id="uczSel" required>
            <option hidden value="">— wybierz ucznia —</option>
            <?php foreach ($uczestnicy as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['n']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Juror:
        <select name="jur" id="jurSel" required>
            <option hidden value="">— wybierz jurora —</option>
            <?php foreach ($jury as $j): ?>
                <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['n']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Kategoria:
        <select name="kat" id="katSel" required>
            <option hidden value="">— wybierz kategorię —</option>
            <?php foreach ($kategorie as $k): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nazwa']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <div id="krytWrap" style="display:none">
        <table>
            <thead><tr><th>Kryterium</th><th>Max</th><th>Punkty</th></tr></thead>
            <tbody id="krytBody"></tbody>
        </table>
        <button type="submit">💾 Zapisz</button>


    </div>
</form>
<a href="panel.php" style="display:block; text-align:center; margin-top: 30px; ">⬅ Powrót do panelu</a>
<a href="wyniki_szczegolowe.php" style="display:block;margin-top:35px;color:#09f">⬅ pełna tabela ocen</a>

<script>
    const kryteria = <?= json_encode($byCat) ?>;
    const oceny    = <?= json_encode($oceny) ?>;
    const uczSel = document.getElementById('uczSel');
    const jurSel = document.getElementById('jurSel');
    const katSel = document.getElementById('katSel');
    const tbody  = document.getElementById('krytBody');
    const wrap   = document.getElementById('krytWrap');

    function updateTable() {
        const kat = katSel.value;
        const ucz = uczSel.value;
        const jur = jurSel.value;

        tbody.innerHTML = '';
        if (!kat || !ucz || !jur) return wrap.style.display = 'none';

        const lista = kryteria[kat] || [];
        lista.forEach(k => {
            const tr = document.createElement('tr');

            const td1 = document.createElement('td');
            td1.textContent = k.nazwa;
            tr.appendChild(td1);

            const td2 = document.createElement('td');
            td2.textContent = k.maks_punkty;
            tr.appendChild(td2);

            const td3 = document.createElement('td');
            const inp = document.createElement('input');
            inp.type = 'number';
            inp.name = `punkty[${k.id}]`;
            inp.min = 0;
            inp.max = k.maks_punkty;
            inp.required = true;
            inp.style.width = '70px';

            // Prefill jeśli ocena istnieje
            const val = (((oceny[ucz] || {})[k.id] || {})[jur]);
            if (val !== undefined) inp.value = val;

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `maks[${k.id}]`;
            hidden.value = k.maks_punkty;

            td3.appendChild(inp);
            td3.appendChild(hidden);
            tr.appendChild(td3);

            tbody.appendChild(tr);
        });

        wrap.style.display = '';
    }

    [uczSel, jurSel, katSel].forEach(el => el.addEventListener('change', updateTable));
</script>
</body>
</html>
