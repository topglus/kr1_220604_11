<?php
include 'config.php';

$id = $_GET['id'];

try {
    $result = $conn->query("SELECT * FROM videogames WHERE id=$id");
    if (!$result) {
        throw new Exception("Ошибка при получении данных: " . $conn->error);
    }
    $game = $result->fetch_assoc();
} catch (Exception $e) {
    echo "<p class='error-message'>" . $e->getMessage() . "</p>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $platform = $_POST['platform'];
    $completion = (int) $_POST['completion_percentage'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE videogames SET title=?, platform=?, completion_percentage=?, status=? WHERE id=?");
    $stmt->bind_param("ssisi", $title, $platform, $completion, $status, $id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать игру</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Редактировать игру</h1>

    <form id="editForm" method="post">
        <label>Название:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($game['title']) ?>" required><br><br>

        <label>Платформа:</label>
        <input type="text" id="platform" name="platform" value="<?= htmlspecialchars($game['platform']) ?>" required><br><br>

        <label>Прогресс (%):</label>
        <input type="number" id="completion" name="completion_percentage" min="0" max="100" value="<?= $game['completion_percentage'] ?>"><br><br>

        <label>Статус:</label>
        <select id="status" name="status">
            <option value="не начата" <?= $game['status'] == 'не начата' ? 'selected' : '' ?>>не начата</option>
            <option value="в процессе" <?= $game['status'] == 'в процессе' ? 'selected' : '' ?>>в процессе</option>
            <option value="завершена" <?= $game['status'] == 'завершена' ? 'selected' : '' ?>>завершена</option>
        </select><br><br>

        <button type="submit">Сохранить</button>
    </form>

    <script>
        const form = document.getElementById('editForm');
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
