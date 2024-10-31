<?php
session_start();

// example.php /
if (isset($_COOKIE["login"]) && isset($_SESSION["session"])) {
    $conn = mysqli_connect("localhost", "root", "", "chat_db");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sender_email = strtolower(mysqli_real_escape_string($conn, $_COOKIE["login"]));

    if (isset($_POST["msg"]) && isset($_POST["userid"])) {

        $msg = validation($_POST["msg"]);
        $receiver_userid = mysqli_real_escape_string($conn, $_POST["userid"]);
        handleTextMessage($conn, $sender_email, $receiver_userid, $msg);
    } elseif (isset($_FILES["file"]) && isset($_POST["receiverid"])) {

        handleFileUpload($conn, $sender_email, $_FILES["file"], $_POST["receiverid"]);
    } else {
        echo "Required parameters are missing.";
    }

    mysqli_close($conn);
} else {
    header("location:login.php");
}

function validation($data)
{
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}

function handleTextMessage($conn, $sender_email, $receiver_userid, $msg)
{
    $sn = 0;
    $rs = mysqli_query($conn, "SELECT MAX(sn) FROM message");
    if ($r = mysqli_fetch_array($rs)) {
        $sn = $r[0];
    }
    $sn++;

    $code = "";
    $a = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
    $b = array_rand($a, 6);
    foreach ($b as $i) {
        $code .= $a[$i];
    }
    $code = $code . "_" . $sn;
    $new_user_id = $code;

    $receiver_email = "";
    $rn = mysqli_prepare($conn, "SELECT email FROM user WHERE userId = ?");
    mysqli_stmt_bind_param($rn, "s", $receiver_userid);
    mysqli_stmt_execute($rn);
    mysqli_stmt_bind_result($rn, $receiver_email);
    mysqli_stmt_fetch($rn);
    mysqli_stmt_close($rn);

    $sender_userid = "";
    $rp = mysqli_prepare($conn, "SELECT userId FROM user WHERE email = ?");
    mysqli_stmt_bind_param($rp, "s", $sender_email);
    mysqli_stmt_execute($rp);
    mysqli_stmt_bind_result($rp, $sender_userid);
    mysqli_stmt_fetch($rp);
    mysqli_stmt_close($rp);

    $dt = date("Y-m-d H:i:s");

    // Insert the message into the message table with is_read and read_status set to 0
    $stmt = mysqli_prepare($conn, "INSERT INTO message (sn, userId, sender_email, sender_userid, receiver_email, receiver_userid, message, chat_time, is_read, read_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)");
    mysqli_stmt_bind_param($stmt, "isssssss", $sn, $new_user_id, $sender_email, $sender_userid, $receiver_email, $receiver_userid, $msg, $dt);

    if (mysqli_stmt_execute($stmt)) {
        echo "success";

        // Update the notifications table for unread messages
        updateNotification($conn, $sender_userid, $receiver_userid);
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

function handleFileUpload($conn, $sender_email, $file, $receiver_userid)
{
    $fileName = basename($file["name"]);
    $fileTmpName = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileError = $file["error"];
    $fileType = $file["type"];

    $allowedTypes = array("image/jpeg", "image/png", "image/gif", "video/mp4", "video/ogg", "video/webm");
    if (in_array($fileType, $allowedTypes) && $fileSize < 50000000) { // 50 MB max for video
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileDestination = 'uploads/' . uniqid('', true) . "." . $fileExtension;
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            $sn = 0;
            $rs = mysqli_query($conn, "SELECT MAX(sn) FROM message");
            if ($r = mysqli_fetch_array($rs)) {
                $sn = $r[0];
            }
            $sn++;

            $code = "";
            $a = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
            $b = array_rand($a, 6);
            foreach ($b as $i) {
                $code .= $a[$i];
            }
            $code = $code . "_" . $sn;
            $new_user_id = $code;

            $receiver_email = "";
            $rn = mysqli_prepare($conn, "SELECT email FROM user WHERE userId = ?");
            mysqli_stmt_bind_param($rn, "s", $receiver_userid);
            mysqli_stmt_execute($rn);
            mysqli_stmt_bind_result($rn, $receiver_email);
            mysqli_stmt_fetch($rn);
            mysqli_stmt_close($rn);

            $sender_userid = "";
            $rp = mysqli_prepare($conn, "SELECT userId FROM user WHERE email = ?");
            mysqli_stmt_bind_param($rp, "s", $sender_email);
            mysqli_stmt_execute($rp);
            mysqli_stmt_bind_result($rp, $sender_userid);
            mysqli_stmt_fetch($rp);
            mysqli_stmt_close($rp);

            $dt = date("Y-m-d H:i:s");

            $stmt = mysqli_prepare($conn, "INSERT INTO message (sn, userId, sender_email, sender_userid, receiver_email, receiver_userid, file_path, chat_time, is_read, read_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)");
            mysqli_stmt_bind_param($stmt, "isssssss", $sn, $new_user_id, $sender_email, $sender_userid, $receiver_email, $receiver_userid, $fileDestination, $dt);

            if (mysqli_stmt_execute($stmt)) {
                echo "success";

                updateNotification($conn, $sender_userid, $receiver_userid);
            } else {
                echo "Error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type or file too large.";
    }
}

function updateNotification($conn, $sender_userid, $receiver_userid)
{

    $checkQuery = "SELECT unread_messages FROM notifications WHERE sender_userid = ? AND receiver_userid = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "ss", $sender_userid, $receiver_userid);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_bind_result($checkStmt, $unread_messages);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);

    if ($unread_messages === null) {

        $insertQuery = "INSERT INTO notifications (sender_userid, receiver_userid, unread_messages) VALUES (?, ?, 1)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ss", $sender_userid, $receiver_userid);
        mysqli_stmt_execute($insertStmt);
        mysqli_stmt_close($insertStmt);
    } else {
        $updateQuery = "UPDATE notifications SET unread_messages = unread_messages + 1 WHERE sender_userid = ? AND receiver_userid = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ss", $sender_userid, $receiver_userid);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    }
}
