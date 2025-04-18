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
    $date_info = $data['date_info'];
    $new_date = $data['new_date'];

    //подготовка SQL-запроса для обновления данных
    $sql = "UPDATE classes SET date_info = ? WHERE user_id = ? AND group_id = ? AND date_info = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siis", $new_date, $user_id, $group_id, $date_info);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Данные о расписании успешно обновлены!!']);

        //создаем редактированную копию записи
        $sql_copy = "INSERT INTO copy_classes (user_id, group_id, student_id, date_info, first_lesson, second_lesson, third_lesson, fourth_lesson, fifth_lesson) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql_copy);
        $stmt2->bind_param("iiissssss",
            $user_id, $group_id, $student_id, $date_info, $first_lesson, $second_lesson, $third_lesson, $fourth_lesson, $first_lesson);
        $stmt2->execute();
    } else {
        echo json_encode(['success' => false, 'message' => 'Данные о расписании не обновлены, причина: ' . $stmt->error]);
    }

} else {
    echo json_encode(["error" => "Invalid request method"]);
}

// Закрытие подключения
$stmt->close();
$conn->close();
?>