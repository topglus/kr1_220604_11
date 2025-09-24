<?php
include 'config.php';

try {
    $result = $conn->query("SELECT * FROM videogames ORDER BY created_at DESC");
    if (!$result) {
        throw new Exception("Ошибка при выполнении запроса: " . $conn->error);
    }
} catch (Exception $e) {
    echo "<p class='error-message'>" . $e->getMessage() . "</p>";
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Коллекция видеоигр</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>Список игр</h1>
<a href="add.php" class="btn btn-add">Добавить игру</a>
<table class="games-table">
    <tr>
        <th>Название</th>
        <th>Платформа</th>
        <th>Прогресс (%)</th>
        <th>Статус</th>
        <th>Дата добавления</th>
        <th>Действия</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['platform']) ?></td>
                <td><?= $row['completion_percentage'] ?></td>
                <td class="status <?php
                    if ($row['status'] == 'не начата') echo 'status-not-started';
                    elseif ($row['status'] == 'в процессе') echo 'status-in-progress';
                    elseif ($row['status'] == 'завершена') echo 'status-completed';
                ?>">
                    <?= $row['status'] ?>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit">Редактировать</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-delete"
                       onclick="return confirm('Вы точно хотите удалить эту игру?');">Удалить</a>
                    <?php if ($row['status'] != 'завершена'): ?>
                        <a href="update_status.php?id=<?= $row['id'] ?>&status=завершена" class="btn btn-status"
                           onclick="return true;">Отметить завершённой</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="empty-message">Игр пока нет...</td>
        </tr>
    <?php endif; ?>
</table>

<?php if (isset($_GET['confirm_complete_id'])): ?>
<script>
let id = <?= (int)$_GET['confirm_complete_id'] ?>;
let proceed = confirm("Прогресс < 100%. Установить прогресс на 100% и сохранить статус 'завершена'?");
if (proceed) {
    window.location.href = "update_status.php?id=" + id + "&status=завершена&set_progress=1";
} else {
    window.location.href = "index.php";
}
</script>
<?php endif; ?>

</body>
</html>
