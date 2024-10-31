<?php
session_start();

if (empty($_POST["email"]) || empty($_POST["pass"])) {
    header("Location: login.php?empty=1");
    exit();
} else {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "chat_db");

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $pass = mysqli_real_escape_string($conn, $_POST["pass"]);

    // Query to check if the email exists
    $rs = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");

    if (mysqli_num_rows($rs) > 0) {
        $r = mysqli_fetch_array($rs);

        // Verify password
        if (password_verify($pass, $r["password"])) {
            setcookie("login", $email, time() + 3600);
            $_SESSION["session"] = $r['userId'];

            // Set login_time, last_activity to the current time, and status to 1
            $userId = $r['userId'];
            $updateQuery = "UPDATE `user` SET `login_time` = NOW(), `last_activity` = NOW(), `status` = 1 WHERE `userId` = '$userId'";
            mysqli_query($conn, $updateQuery);

            // Redirect to Dashboard
            header("Location: Dashboard.php");
            exit();
        } else {
            header("Location: login.php?invalid_password=1");
            exit();
        }
    } else {
        header("Location: login.php?invalid_record=1");
        exit();
    }

    mysqli_close($conn);
}
?>
