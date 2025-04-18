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
    $student_id = $_GET['student_id'];
    $from = $_GET['from'];
    $to = $_GET['to'];

    // Подготовка запроса
    $sql = "SELECT `first_lesson`, `second_lesson`, `third_lesson`, `fourth_lesson`, `fifth_lesson` FROM classes 
            WHERE user_id = ? AND group_id = ? AND student_id = ? 
              AND STR_TO_DATE(date_info, '%d%m%Y') BETWEEN STR_TO_DATE(?, '%d%m%Y') AND STR_TO_DATE(?, '%d%m%Y')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiiss', $user_id, $group_id, $student_id, $from, $to);
    $stmt->execute();
    $result = $stmt->get_result();

    // Подсчет оценок
    $counts = ['н' => 0, 'о' => 0, 'б' => 0];

    // Предполагается, что $result содержит результаты выполнения SQL-запроса
    while ($row = $result->fetch_assoc()) {
        // Итерируем по каждому уроку в строке
        foreach ($row as $lesson) {
            if (array_key_exists($lesson, $counts)) {
                $counts[$lesson]++;
            }
        }
    }

    $jsonResult = json_encode($counts, JSON_UNESCAPED_UNICODE);

    echo $jsonResult;
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$stmt->close();
$conn->close();
?>