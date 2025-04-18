<?php
// Разрешаем запросы с любых доменов (или укажите конкретный домен)
header("Access-Control-Allow-Origin: *");
// Разрешаем методы HTTP, которые могут быть использованы для запросов
header("Access-Control-Allow-Methods: PATCH, OPTIONS");
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

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // Получение данных из POST-запроса
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    $user_id = $data['user_id'];
    $group_id = $data['group_id'];
    $id = $data['id'];
    $name = $data['name'];

    $sql = "UPDATE lessons SET name = ? WHERE user_id = ? AND group_id = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $name, $user_id, $group_id, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Данные о предмете успешно обновлены!!']);

        //редактируем копию записи
        $sql_copy = "UPDATE copy_lessons SET name = ? WHERE user_id = ? AND group_id = ? AND id = ?";
        $stmt2 = $conn->prepare($sql_copy);
        $stmt2->bind_param("siii", $name, $user_id, $group_id, $id);
        $stmt2->execute();
    } else {
        echo json_encode(['success' => false, 'message' => 'Данные о предмете не обновлены, причина: ' . $stmt->error]);
    }

} else {
    echo json_encode(["error" => "Invalid request method"]);
}

// Закрытие подключения
$stmt->close();
$conn->close();
?>