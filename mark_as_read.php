<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$sender_id = $_POST['sender_id'];


$sql = "UPDATE message SET is_read = 1 WHERE sender_userid = ? AND receiver_userid = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $sender_id, $user_id);
$stmt->execute();

echo json_encode(['status' => 'success']);
$stmt->close();
$conn->close();
