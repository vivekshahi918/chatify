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


    $rs = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");

    if (mysqli_num_rows($rs) > 0) {
        $r = mysqli_fetch_array($rs);

        if (password_verify($pass, $r["password"])) {

            setcookie("login", $email, time() + 3600);
            $_SESSION["session"] = $r['userId'];


            $userId = $r['userId'];
            $updateQuery = "UPDATE `user` SET `login_time` = NOW(), `last_activity` = NOW() WHERE `userId` = '$userId'";
            mysqli_query($conn, $updateQuery);


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
}
