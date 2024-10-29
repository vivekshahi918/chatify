<?php
// Start session to get logged-in user ID
session_start();

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "chat_db");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Logged-in user ID
$user_id = $_SESSION['user_id'];

// Query to fetch the sum of unread messages for the logged-in user
$sql = "SELECT SUM(unread_messages) AS total_unread
        FROM notifications
        WHERE receiver_userid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_unread = $row['total_unread'] ?? 0; // Default to 0 if no unread messages

// Return the unread count as JSON
echo json_encode(['status' => 'success', 'total_unread' => $total_unread]);

// Close the database connection
$stmt->close();
$conn->close();
?>
