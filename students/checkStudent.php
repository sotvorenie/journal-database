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
    $user_id = $_GET['user_id'];
    $group_id = $_GET['group_id'];
    $id = $_GET['id'];
    $name = $_GET['name'];
    $second_name = $_GET['second_name'];
    $surname = $_GET['surname'];

    $sql = "SELECT * FROM students WHERE user_id = ? AND group_id = ?";
    $params = [$user_id, $group_id];
    $types = "ii";

    if (!empty($id)) {
        $sql .= " AND id = ?";
        $params[] = $id;
        $types .= "i";
    }

    if (!empty($name) && !empty($second_name) && !empty($surname)) {
        $sql .= " AND name = ? AND second_name = ? AND surname = ?";
        $params[] = $name;
        $params[] = $second_name;
        $params[] = $surname;
        $types .= "sss";
    }

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