<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT COUNT(*) AS total_unread
        FROM message
        WHERE receiver_userid = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_unread = $row['total_unread'] ?? 0;

echo json_encode(['status' => 'success', 'total_unread' => $total_unread]);

$stmt->close();
$conn->close();
