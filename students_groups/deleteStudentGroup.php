<?php
// Разрешаем запросы с любых доменов (или укажите конкретный домен)
header("Access-Control-Allow-Origin: *");
// Разрешаем методы HTTP, которые могут быть использованы для запросов
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
// Разрешаем заголовки, которые могут быть использованы в запросах
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Разрешаем использование учетных данных
header("Access-Control-Allow-Credentials: true");

include_once '../database.php';

$database = new DB();
$conn = $database->getConnection();
$stmt = '';

// Обработка OPTIONS-запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {


    $user_id = $_GET['user_id'];
    $id = $_GET['id'];

    $sql = "DELETE FROM students_groups WHERE user_id = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Группа удалена успешно!!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Группа не была удалена, причина: ' . $stmt->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>