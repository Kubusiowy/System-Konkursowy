<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Połączenie z bazą danych
$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Błąd bazy danych"]);
    exit;
}

// Odczytaj dane JSON
$data = json_decode(file_get_contents("php://input"), true);

// Sprawdź dane wejściowe
if (
    !isset($data['jury_id']) ||
    !isset($data['uczestnik_id']) ||
    !isset($data['kryterium_id']) ||
    !isset($data['wartosc'])
) {
    http_response_code(400);
    echo json_encode(["error" => "Złe żądanie"]);
    exit;
}

$jury_id = (int)$data['jury_id'];
$uczestnik_id = (int)$data['uczestnik_id'];
$kryterium_id = (int)$data['kryterium_id'];
$wartosc = (int)$data['wartosc'];

// Sprawdź, czy ocena już istnieje
$stmt = $conn->prepare("SELECT id FROM oceny WHERE juror_id=? AND uczestnik_id=? AND kryterium_id=?");
$stmt->bind_param("iii", $jury_id, $uczestnik_id, $kryterium_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Aktualizacja istniejącej oceny
    $stmt = $conn->prepare("UPDATE oceny SET punkty=? WHERE juror_id=? AND uczestnik_id=? AND kryterium_id=?");
    $stmt->bind_param("iiii", $wartosc, $jury_id, $uczestnik_id, $kryterium_id);
} else {
    // Dodanie nowej oceny
    $stmt = $conn->prepare("INSERT INTO oceny (juror_id, uczestnik_id, kryterium_id, punkty) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $jury_id, $uczestnik_id, $kryterium_id, $wartosc);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Nie udało się zapisać"]);
}
