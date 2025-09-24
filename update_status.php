<?php
include 'config.php';

$id = $_GET['id'];
$status = $_GET['status'];

try {
    $stmt = $conn->prepare("UPDATE videogames SET status=? WHERE id=?");
    if (!$stmt) {
        throw new Exception("Ошибка подготовки запроса: " . $conn->error);
    }
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
} catch (Exception $e) {
    echo "<p class='error-message'>" . $e->getMessage() . "</p>";
    exit;
}

header("Location: index.php");
exit;
