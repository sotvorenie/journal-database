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

    foreach ($data as $item) {
        $user_id = $conn->real_escape_string($item['user_id']);
        $group_id = $conn->real_escape_string($item['group_id']);
        $student_id = $conn->real_escape_string($item['student_id']);
        $date_info = $conn->real_escape_string($item['date_info']);
        $first_lesson = $conn->real_escape_string($item['first_lesson']);
        $second_lesson = $conn->real_escape_string($item['second_lesson']);
        $third_lesson = $conn->real_escape_string($item['third_lesson']);
        $fourth_lesson = $conn->real_escape_string($item['fourth_lesson']);
        $fifth_lesson = $conn->real_escape_string($item['fifth_lesson']);

        $sql = "INSERT INTO classes (user_id, group_id, student_id, date_info, first_lesson, second_lesson, third_lesson, fourth_lesson, fifth_lesson) VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiissssss",
            $user_id, $group_id, $student_id, $date_info, $first_lesson, $second_lesson, $third_lesson, $fourth_lesson, $first_lesson);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Данные о расписании добавлены успешно!!']);

            //создаем копию записи
            $sql_copy = "INSERT INTO copy_classes (user_id, group_id, student_id, date_info, first_lesson, second_lesson, third_lesson, fourth_lesson, fifth_lesson) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $conn->prepare($sql_copy);
            $stmt2->bind_param("iiissssss",
                $user_id, $group_id, $student_id, $date_info, $first_lesson, $second_lesson, $third_lesson, $fourth_lesson, $first_lesson);
            $stmt2->execute();
        } else {
            echo json_encode(['success' => false, 'message' => 'Расписание не было добавлено, причина: ' . $stmt->error]);
        }
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$stmt->close();
$conn->close();
?>