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
    //$data = json_decode(file_get_contents('php://input'), true);

    $id = $_GET['id'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die($conn->error);
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        $stmt->close();
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die($conn->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            die($stmt->error);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Пользователь удален успешно!!']);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>