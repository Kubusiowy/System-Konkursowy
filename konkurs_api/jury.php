<?php


$conn = new mysqli("localhost", "root", "", "konkurs");
if ($conn->connect_error) die("Błąd połączenia z bazą danych");

$result = $conn->query("SELECT * FROM jury");
$jury = [];

while ($row = $result->fetch_assoc()) {
    $jury[] = $row;
}

header('Content-Type: application/json');
echo json_encode($jury);


?>
