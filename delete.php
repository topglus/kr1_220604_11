<?php
include 'config.php';

$id = $_GET['id'];

try {
    $result = $conn->query("DELETE FROM videogames WHERE id=$id");
    if (!$result) {
        throw new Exception("Ошибка при удалении: " . $conn->error);
    }
} catch (Exception $e) {
    echo "<p class='error-message'>" . $e->getMessage() . "</p>";
    exit;
}

header("Location: index.php");
exit;
