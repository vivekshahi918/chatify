<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION["session"])) {
    $userId = $_SESSION["session"];

    // Set last_Activity to logout_time and update status to 0
    $updateQuery = "UPDATE user SET logout_time = NOW(), last_activity = NOW(), status = 0 WHERE userId = '$userId'";
    mysqli_query($conn, $updateQuery);
}

setcookie("login", "", time() - 1);
session_unset();
session_destroy();
header("Location: index.php");

mysqli_close($conn);
?>
