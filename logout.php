<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION["session"])) {
    $userId = $_SESSION["session"];


    $updateQuery = "UPDATE `user` SET `logout_time` = NOW() WHERE `userId` = '$userId'";
    mysqli_query($conn, $updateQuery);
}


setcookie("login", "", time() - 1);
session_unset();
session_destroy();
header("location: index.php");
