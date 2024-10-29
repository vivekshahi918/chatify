<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Define session timeout duration (in seconds)
$session_timeout = 15 * 60; // 15 minutes

// Check if user is logged in
if (isset($_COOKIE["login"]) && isset($_SESSION["session"])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
        $userId = $_SESSION["session"];

        // Update logout_time in the database
        $updateQuery = "UPDATE `user` SET `logout_time` = NOW() WHERE `userId` = '$userId'";
        mysqli_query($conn, $updateQuery);

        // Clear the session and cookie
        setcookie("login", "", time() - 1);
        session_unset();
        session_destroy();

        // Redirect to login page
        header("location: login.php");
        exit();
    }

    // Update last activity timestamp
    $_SESSION['last_activity'] = time();

    $email = $_COOKIE["login"];
    $session = $_SESSION["session"];

    // Fetch logged-in user info
    $rs = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
    if ($rs && mysqli_num_rows($rs) > 0) {
        $r = mysqli_fetch_assoc($rs);
        $userId = $r["userId"];
    } else {
        echo "User data not found.";
        header("location: login.php");
        exit();
    }

    // Fetch total unread notifications for logged-in user
    $notificationQuery = "SELECT SUM(unread_messages) AS total_unread FROM notifications WHERE receiver_userid = '$userId'";
    $notificationResult = mysqli_query($conn, $notificationQuery);
    $totalUnreadNotifications = ($notificationResult && mysqli_num_rows($notificationResult) > 0) ? mysqli_fetch_assoc($notificationResult)['total_unread']+ 0 : 0;
} else {
    header("location: login.php");
    exit();
}
?>

<?php include_once "header.php"; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Styles and Scripts from your existing code -->
<style>
    body {
        background-color: #f0f8ff; /* Light Alice Blue */
        font-family: Arial, sans-serif;
    }
    hr {
        border: 5px solid #4caf50; /* Green */
        border-radius: 2px;
    }
    .background-image {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: -1; /* Ensure the background is behind the content */
    }
    #image-background {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensure the image covers the entire background */
    }
    .notification-icon {
        position: relative;
        font-size: 24px;
        color: #333; /* Icon color */
        cursor: pointer;
        transition: color 0.3s ease;
    }
    .notification-icon:hover {
        color: #02cefc; /* Change color on hover */
    }
    .notification-badge {
        position: absolute;
        top: -3px;
        right: 1px;
        background-color: #ff0000; /* Red badge color */
        color: white;
        border-radius: 50%;
        padding: 3px 7px;
        font-size: 10px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Black transparent overlay, initially hidden */
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6); /* Black with 50% transparency */
        opacity: 0;
        transition: opacity 0.5s ease;
        z-index: -1; /* Initially behind the content */
    }
    .card {
        max-width: 100%;          /* Allows card to be full-width within the container */
        width: 100%; 
        height: 95%;              /* Keeps the card within the column width */
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: box-shadow 0.3s ease-in-out;
        margin: 0 auto;            /* Centers the card within the column */
    }
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    .card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        z-index: 0;
        transition: background 0.5s, transform 0.5s;
        box-shadow: 
            1px 1px 20px #02cefcf7,
            1px 1px 60px #ff6b6bf7,
            1px 1px 80px #fffb96f7,
            1px 1px 100px #87ceebf7;
    }
    /* Show overlay when card is hovered */
    .card:hover ~ .overlay {
        opacity: 1;
    }
    .btn-primary {
        background-color: #007bff; /* Bootstrap Primary Color */
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #0056b3; /* Darker Blue */
    }
    .chat-link {
        color: #333; /* Set the default color for the chat link */
        text-decoration: none; /* Remove underline */
    }
    .chat-link:visited {
        color: #333; /* Maintain the same color for visited links */
    }
    .chat-link:hover {
        color: #007bff; /* Set a different color on hover */
    }
    #Search {
        padding: 8px;
        margin: 8px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 100%;
        box-sizing: border-box;
    }
    .table-borderless tr {
        transition: background-color 0.3s ease;
    }
    .table-borderless tr:hover {
        background-color: #e6f7ff; /* Light Blue */
    }
    .rounded-circle {
        border: 2px solid #007bff; /* Primary Color Border */
        transition: border-color 0.3s ease;
    }
    .rounded-circle:hover {
        border-color: #0056b3; /* Darker Blue Border */
    }
    .text {
        margin: 15px 0;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("input#Search").keyup(function() {
            var searchValue = $(this).val();
            var email = "<?php echo $email; ?>";
            if (searchValue === "") {
                $.post("new_dashboard.php", { email: email }, function(data) {
                    $("#record").html(data);
                });
            } else {
                $.post("search.php", { ch: searchValue }, function(data) {
                    $("#record").html(data);
                });
            }
        });

        // Refresh unread notifications count every 30 seconds
        setInterval(function() {
            $.ajax({
                url: "get_notifications.php", // Create a file that returns total unread count
                type: "POST",
                data: { receiver_userid: "<?php echo $userId; ?>" },
                success: function(data) {
                    $(".notification-badge").text(data); // Update the notification count badge
                }
            });
        }, 30000);

        setInterval(function() {
                                location.reload();
                            }, 10000);
    });
    
</script>

<body>
<div class="background-image">
                            <img src="image/logo.png" id="image-background">
                        </div>
<div class="container-fluid">
<div class="row justify-content-center" style="margin-top: 90px;">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <table class="table table-borderless">
                        <tr>
                            <td>
                                <img src="images/<?php echo $r["userId"]; ?>.jpg" class="rounded-circle" style="width:80px;height:80px;">
                            </td>
                            <td>
                                <?php echo $r["first_name"] . " " . $r["last_name"]; ?>
                                <?php if ($r["last_activity"] !== $r["logout_time"]): ?>
                                    <br><strong>Active Now</strong>
                                <?php else: ?>
                                    <br><strong>Inactive</strong>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-primary">
                                    <a href="logout.php" style="text-decoration:none;color:white">Logout</a>
                                </button>
                            </td>
                            <td>
                                <!-- Notification icon with dynamic unread count -->
                                <div class="notification-icon">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge"><?php echo $totalUnreadNotifications; ?></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
                <div class="row">
                    <span class="text">Select a user to start chat</span>
                    <input type="text" id="Search" placeholder="Search here.......">
                </div>
                <div class="row" id="record">
                    <?php
                    // Fetch other users' info along with unread messages count
                    $rp = mysqli_query($conn, "SELECT u.*, COALESCE(n.unread_messages, 0) AS unread_messages 
                                FROM user u
                                LEFT JOIN notifications n ON u.userId = n.sender_userid AND n.receiver_userid = '$userId'
                                WHERE u.email <> '$email'
                                ORDER BY u.status DESC, u.first_name ASC");

                    echo "<table class='table table-borderless'>";
                    while ($rn = mysqli_fetch_array($rp)) {
                        ?>
                        <tr>
                        <td>
                            <a href="chat.php?userid=<?php echo $rn["userId"]; ?>" class="chat-link">
                                <img src="images/<?php echo $rn["userId"]; ?>.jpg" class="rounded-circle" style="width:60px;height:60px;">
                                <span><?php echo $rn["first_name"] . " " . $rn["last_name"]; ?></span>
                                <?php if ($rn['unread_messages'] > 0): ?>
                                    <span class="badge bg-primary"><?php echo $rn['unread_messages']; ?> New</span>
                                <?php endif; ?>
                            </a>
                        </td>

                            <td>
                                <?php if ($rn["last_activity"] !== $rn["logout_time"]): ?>
                                    <span style="color: green;">● Active</span>
                                <?php else: ?>
                                    <span style="color: red;">● Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Individual notification icon for each user -->
                                <div class="notification-icon">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge"><?php echo $rn['unread_messages']; ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    echo "</table>";
                    ?>
                </div>
            </div>
        </div>
        <div class="overlay"></div>
    </div>
</div>
</div>
</body>
</html>
