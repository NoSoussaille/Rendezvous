<?php
require_once "db_connexion.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $date = $input['date'] ?? null;

    if ($date) {
        $query = "SELECT DATE_FORMAT(date_rdv, '%H') AS hour FROM rendezvous WHERE DATE(date_rdv) = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $reservedSlots = [];
        while ($row = $result->fetch_assoc()) {
            $reservedSlots[] = (int)$row['hour'];
        }

        echo json_encode($reservedSlots);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Date non fournie']);
    }
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Méthode non autorisée']);
exit();
?>