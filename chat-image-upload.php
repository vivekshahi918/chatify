<?php
session_start();
if (isset($_COOKIE["login"]) && isset($_SESSION["session"])) {
    $conn = mysqli_connect("localhost", "root", "", "chat_db");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($conn, $_COOKIE["login"]);
    $session = $_SESSION["session"];

    if (isset($_FILES["file"]) && isset($_POST["receiverid"])) {
        $file = $_FILES["file"];
        $receiverId = mysqli_real_escape_string($conn, $_POST["receiverid"]);
        $fromUserId = mysqli_fetch_assoc(mysqli_query($conn, "SELECT userId FROM user WHERE email='$email'"))['userId'];

        $fileName = basename($file["name"]);
        $fileTmpName = $file["tmp_name"];
        $fileSize = $file["size"];
        $fileError = $file["error"];
        $fileType = $file["type"];

        // Validate file type and size
        $allowedTypes = array("image/jpeg", "image/png", "image/gif");
        if (in_array($fileType, $allowedTypes) && $fileSize < 5000000) { // 5 MB max
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $fileDestination = 'uploads/' . uniqid('', true) . "." . $fileExtension;
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // Insert file information into database
                $stmt = $conn->prepare("INSERT INTO message (sender_userid, receiver_userid, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $fromUserId, $receiverId, $fileDestination);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'file_path' => $fileDestination]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error inserting file information into database.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error uploading file.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type or file too large.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded or receiver ID not specified.']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
}
?>
