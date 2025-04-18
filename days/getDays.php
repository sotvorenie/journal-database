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
    $day = isset($_GET['day']) ? $_GET['day'] : '';
    $month = isset($_GET['month']) ? $_GET['month'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';
    $offset = $_GET['offset'];
    $limit = $_GET['limit'];

    //подготовка SQL-запроса для обновления данных
    $sql = "SELECT * FROM days WHERE 1=1";
    $params = [];
    $types = "";

    if ($day !== '') {
        $sql .= " AND day = ?";
        $params[] = $day;
        $types .= "s";
    }
    if ($month !== '') {
        $sql .= " AND month = ?";
        $params[] = $month;
        $types .= "s";
    }
    if ($year !== '') {
        $sql .= " AND year = ?";
        $params[] = $year;
        $types .= "s";
    }

    //$sql = rtrim($sql, ", ");
    $sql .= " AND user_id = ? AND group_id = ? ORDER BY STR_TO_DATE(date_info, '%d%m%Y') DESC LIMIT ?, ?";
    $params[] = $user_id;
    $params[] = $group_id;
    $params[] = $offset;
    $params[] = $limit;
    $types .= "iiii";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Подготовка SQL-запроса для подсчета общего количества записей
    $countSql = "SELECT COUNT(*) as total FROM days WHERE 1=1";
    $countParams = [];
    $countTypes = "";

    if ($day !== '') {
        $countSql .= " AND day = ?";
        $countParams[] = $day;
        $countTypes .= "s";
    }
    if ($month !== '') {
        $countSql .= " AND month = ?";
        $countParams[] = $month;
        $countTypes .= "s";
    }
    if ($year !== '') {
        $countSql .= " AND year = ?";
        $countParams[] = $year;
        $countTypes .= "s";
    }

    $countSql .= " AND user_id = ? AND group_id = ?";
    $countParams[] = $user_id;
    $countParams[] = $group_id;
    $countTypes .= "ii";

    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param($countTypes, ...$countParams);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();
    $totalRecords = $countRow['total'];

    // Вычисление количества оставшихся записей
    $remaining = max(0, $totalRecords - ($offset + count($data)));

    // Формирование ответа
    $response = [
        'data' => $data,
        'remaining' => $remaining
    ];

    // Возвращаем ответ в формате JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$stmt->close();
$conn->close();
?>