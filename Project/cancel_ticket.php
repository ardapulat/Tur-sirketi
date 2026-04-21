<?php
require_once 'config.php'; // session varsa burada

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['ticket_id'])) {
    header("Location: biletlerim.html?error=invalid");
    exit;
}

$user_id  = (int)$_SESSION['user_id'];
$ticket_id = (int)$_POST['ticket_id'];

$conn = new mysqli("localhost", "root", "", "pru_turizm");
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası");
}

$stmt = $conn->prepare("
    DELETE FROM purchases 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: biletlerim.html?success=deleted");
} else {
    header("Location: biletlerim.html?error=not_allowed");
}

exit;
