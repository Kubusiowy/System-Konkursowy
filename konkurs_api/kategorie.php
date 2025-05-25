<?php
$conn = new mysqli("localhost", "root", "", "konkurs");
$result = $conn->query("SELECT * FROM kategorie");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>