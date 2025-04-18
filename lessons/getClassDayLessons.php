<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once '../database.php';

$database = new DB();
$conn = $database->getConnection();
$stmt = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Получение параметров user_id, group_id, offset и limit из GET-запроса
    $user_id = $_GET['user_id'];
    $group_id = $_GET['group_id'];
    $id1 = isset($_GET['id1']) ? $_GET['id1'] : '';
    $id2 = isset($_GET['id2']) ? $_GET['id2'] : '';
    $id3 = isset($_GET['id3']) ? $_GET['id3'] : '';
    $id4 = isset($_GET['id4']) ? $_GET['id4'] : '';
    $id5 = isset($_GET['id5']) ? $_GET['id5'] : '';

    $sql = "SELECT * FROM lessons 
         WHERE user_id = ? AND group_id = ? AND id IN (?, ?, ?, ?, ?)
         ORDER BY name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiii", $user_id, $group_id, $id1, $id2, $id3, $id4, $id5);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$stmt->close();
$conn->close();
?><?php
