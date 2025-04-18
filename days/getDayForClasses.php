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
    $date_info = $_GET['date_info'] ?? '';

    //подготовка SQL-запроса для обновления данных
    $sql = "SELECT * FROM days WHERE ";
    $params = [];
    $types = "";

    if ($date_info !== '') {
        $sql .= "date_info = ? AND ";
        $params[] = $date_info;
        $types .= "s";
    }

    $sql = rtrim($sql, ", ");
    $sql .= " user_id = ? AND group_id = ? ORDER BY date_info DESC LIMIT 0, 1";
    $params[] = $user_id;
    $params[] = $group_id;
    $types .= "ii";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

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
?>