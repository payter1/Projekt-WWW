<?php
require_once 'connect.php';

// Zabezpieczenie dla pewności
if (!isset($_SESSION['logged']) || $_SESSION['userLVL'] == 0) {
    exit;
}

if (isset($_GET['q'])) {
    $q = $conn->real_escape_string(trim($_GET['q']));
    
    // Szukamy po dokładnym ID lub pasującym imieniu
    $task = "SELECT id, name FROM donkeys WHERE id = '$q' OR name LIKE '%$q%' LIMIT 8";
    $query = $conn->query($task);
    
    $results = [];
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $results[] = $row;
        }
    }
    
    // Zwracamy wynik w formacie JSON dla jQuery
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}
?>