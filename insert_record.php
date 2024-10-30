<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if (empty($_POST["fname"]) || empty($_POST["lname"]) || empty($_POST["email"]) || empty($_POST["pass"])) {
    header("Location: index.php?empty=1");
    exit();
}

$fname = mysqli_real_escape_string($conn, $_POST["fname"]);
$lname = mysqli_real_escape_string($conn, $_POST["lname"]);
$email = mysqli_real_escape_string($conn, $_POST["email"]);
$pass = mysqli_real_escape_string($conn, $_POST["pass"]);


if (!preg_match("/^[a-zA-Z]+$/", $fname) || !preg_match("/^[a-zA-Z]+$/", $lname)) {
    header("Location: index.php?invalid_name=1");
    exit();
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: index.php?invalid_email=1");
    exit();
}


$result = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
if (mysqli_fetch_array($result)) {
    header("Location: index.php?already=1");
    exit();
}


$sn = mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(sn) FROM user"))[0] + 1;
$code = generateUniqueId($sn);


$target = "images/" . $code . ".jpg";
if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
    header("Location: index.php?img_err=1");
    exit();
}

$password = password_hash($pass, PASSWORD_BCRYPT);

$dt = date("d M, Y");
$query = "INSERT INTO user (sn, userId, first_name, last_name, email, password, status, login_time)
          VALUES ($sn, '$code', '$fname', '$lname', '$email', '$password', 1, '$dt')";
if (mysqli_query($conn, $query)) {
    header("Location: index.php?record_inserted=1");
} else {
    header("Location: index.php?again=1");
}

mysqli_close($conn);


function generateUniqueId($sn)
{
    $characters = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
    $uniqueId = implode('', array_map(function ($char) use ($characters) {
        return $characters[array_rand($characters)];
    }, range(1, 6)));
    return $uniqueId . "_" . $sn;
}
