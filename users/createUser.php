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
    $login = $data['login'];
    $password = $data['password'];
    $organization = $data['organization'];

    $sql = "INSERT INTO users (login, password, organization) VALUES
        (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $login, $password, $organization);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Пользователь был успешно добавлен']);

        //получаем id созданного пользователя
        $id = $conn->insert_id;

        //создаем копию записи
        $sql_copy = "INSERT INTO copy_users (id, login, password, organization) VALUES
        (?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql_copy);
        $stmt2->bind_param("isss", $id, $login, $password, $organization);
        $stmt2->execute();
    } else {
        echo json_encode(['success' => false, 'message' => 'Пользователь не был добавлен, причина: ' . $stmt->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$stmt->close();
$conn->close();
?>