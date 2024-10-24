<?php
session_start();
if (isset($_COOKIE["login"]) && isset($_SESSION["session"])) {
    $conn = mysqli_connect("localhost", "root", "", "chat_db");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($conn, $_COOKIE["login"]);

    if (isset($_REQUEST["ch"])) {
        $ch = mysqli_real_escape_string($conn, $_REQUEST["ch"]);

        // Improved query with correct precedence for AND/OR conditions
        $query = "SELECT * FROM user WHERE (first_name LIKE '%$ch%' OR last_name LIKE '%$ch%') AND email != '$email'";
        $rs = mysqli_query($conn, $query);

        $flag = 0;
        echo "<table class='table table-borderless'>";

        while ($r = mysqli_fetch_array($rs)) {
            $flag++;
            echo "<tr>
                <td>
                    <a href='chat.php?userid=" . $r["userId"] . "' style='text-decoration:none;color:black;display:flex;align-items:center;'>
                        <img src='images/" . $r["userId"] . ".jpg' class='rounded-circle' style='width:60px;height:60px;'>
                        <span style='margin-left: 10px;'>" . $r["first_name"] . " " . $r["last_name"] . "</span>
                    </a>
                </td>
            </tr>";
        }
        echo "</table>";

        if ($flag == 0) {
            echo "<div class='alert alert-warning'>Record Not Found</div>";
        }
    }

    mysqli_close($conn);
} else {
    header("location:login.php");
}
?>