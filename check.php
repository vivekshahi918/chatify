<?php
session_start();

if (empty($_POST["email"]) || empty($_POST["pass"])) {
    // Redirect if email or password is missing
    header("Location: login.php?empty=1");
    exit();
} else {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "chat_db");

    // Check if connection was successful
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Secure email and password input
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $pass = mysqli_real_escape_string($conn, $_POST["pass"]);

    // Query to check if the user exists
    $rs = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");

    if (mysqli_num_rows($rs) > 0) {
        $r = mysqli_fetch_array($rs);
        // Check if the password matches
        if (password_verify($pass, $r["password"])) {
            // Set session and cookie for login
            setcookie("login", $email, time() + 3600);
            $_SESSION["session"] = $r['userId'];

            // Update login_time and last_activity on successful login
            $userId = $r['userId'];
            $updateQuery = "UPDATE `user` SET `login_time` = NOW(), `last_activity` = NOW() WHERE `userId` = '$userId'";
            mysqli_query($conn, $updateQuery);

            // Redirect to Dashboard
            header("Location: Dashboard.php");
            exit();
        } else {
            // Invalid password
            header("Location: login.php?invalid_password=1");
            exit();
        }
    } else {
        // No user found with this email
        header("Location: login.php?invalid_record=1");
        exit();
    }
}
?>
