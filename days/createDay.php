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
    $date_info = $data['date_info'];
    $day = $data['day'];
    $month = $data['month'];
    $year = $data['year'];
    $first_lesson = $data['first_lesson'];
    $second_lesson = $data['second_lesson'];
    $third_lesson = $data['third_lesson'];
    $fourth_lesson = $data['fourth_lesson'];
    $fifth_lesson = $data['fifth_lesson'];

    $sql = "INSERT INTO days (user_id, group_id, date_info, day, month, year, first_lesson, second_lesson, third_lesson, fourth_lesson, fifth_lesson) VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssiiiii",
        $user_id, $group_id, $date_info, $day, $month, $year, $first_lesson, $second_lesson, $third_lesson, $fourth_lesson, $fifth_lesson);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Данные о дне добавлены успешно!!']);

        //создаем копию записи
        $sql_copy = "INSERT INTO copy_days (user_id, group_id, date_info, day, month, year, first_lesson, second_lesson, third_lesson, fourth_lesson, fifth_lesson) VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql_copy);
        $stmt2->bind_param("iissssiiiii",
            $user_id, $group_id, $date_info, $day, $month, $year, $first_lesson, $second_lesson, $third_lesson, $fourth_lesson, $fifth_lesson);
        $stmt2->execute();
    } else {
        echo json_encode(['success' => false, 'message' => 'День не был добавлен, причина: ' . $stmt->error]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$stmt->close();
$conn->close();
?>