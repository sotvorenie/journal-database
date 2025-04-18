<?php
// Разрешаем запросы с любых доменов (или укажите конкретный домен)
header("Access-Control-Allow-Origin: *");
// Разрешаем методы HTTP, которые могут быть использованы для запросов
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из POST-запроса
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    // Получение данных из POST-запроса
    $user_id = $data['user_id'];
    $group_id = $data['group_id'];
    $name = $data['name'];
    $second_name = $data['second_name'];
    $surname = $data['surname'];

    $sql = "INSERT INTO students (user_id, group_id, name, second_name, surname) VALUES 
        (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss",
        $user_id, $group_id, $name, $second_name, $surname);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Данные о студенте добавлены успешно!!']);

        //получаем id созданного студента
        $id = $conn->insert_id;

        //создаем копию записи
        $sql_copy = "INSERT INTO copy_students (user_id, group_id, id, name, second_name, surname) VALUES
        (?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql_copy);
        $stmt2->bind_param("iiisss", $user_id, $group_id, $id, $name, $second_name, $surname);
        $stmt2->execute();
    } else {
        echo json_encode(['success' => false, 'message' => 'Студен не был добавлен, причина: ' . $stmt->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$stmt->close();
$conn->close();
?>