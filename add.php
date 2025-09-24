<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $platform = $_POST['platform'];
    $completion = (int) $_POST['completion_percentage'];
    $status = $_POST['status'];

    try {
        $stmt = $conn->prepare("INSERT INTO videogames (title, platform, completion_percentage, status) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        }
        $stmt->bind_param("ssis", $title, $platform, $completion, $status);
        $stmt->execute();
    } catch (Exception $e) {
        echo "<p class='error-message'>" . $e->getMessage() . "</p>";
        exit;
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить игру</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Добавить игру</h1>
<form id="addForm" method="post">
    Название: <input type="text" name="title" required><br><br>
    Платформа: <input type="text" name="platform" required><br><br>
    Прогресс (%): <input type="number" id="completion" name="completion_percentage" min="0" max="100" value="0"><br><br>
    Статус:
    <select id="status" name="status">
        <option value="не начата">не начата</option>
        <option value="в процессе">в процессе</option>
        <option value="завершена">завершена</option>
    </select><br><br>
    <button type="submit">Сохранить</button>
</form>

<script>
const form = document.getElementById('addForm');
const completion = document.getElementById('completion');
const status = document.getElementById('status');

function checkWarning() {
    const prog = parseInt(completion.value) || 0;
    const stat = status.value;
    let message = '';

    if (stat === 'завершена' && prog < 100) {
        message = "Статус 'завершена', но прогресс < 100%.";
    } else if (prog === 100 && stat !== 'завершена') {
        message = "Прогресс 100%, рекомендуется статус 'завершена'.";
    } else if (prog > 0 && prog < 100 && stat !== 'в процессе') {
        message = `Прогресс ${prog}%, рекомендуется статус 'в процессе'.`;
    } else if (prog === 0 && stat !== 'не начата') {
        message = "Прогресс 0%, рекомендуется статус 'не начата'.";
    }

    return message;
}

form.addEventListener('submit', function(e) {
    const warning = checkWarning();
    if (warning) {
        const proceed = confirm(warning + "\nВы хотите сохранить изменения?");
        if (!proceed) {
            e.preventDefault();
        }
    }
});
</script>
</body>
</html>
