<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$session_timeout = 5 * 60; // 15 minutes

if (isset($_SESSION["session"])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
        $userId = $_SESSION["session"];
        $updateQuery = "UPDATE `user` SET `logout_time` = NOW() WHERE `userId` = '$userId'";
        mysqli_query($conn, $updateQuery);

        setcookie("login", "", time() - 1);
        session_unset();
        session_destroy();

        header("location: index.php");
        exit();
    }

    $_SESSION['last_activity'] = time();

    if (isset($_COOKIE["login"]) && isset($_SESSION["session"])) {
        $email = mysqli_real_escape_string($conn, $_COOKIE["login"]);
        $session = $_SESSION["session"];
        $rs2 = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
        $r2 = mysqli_fetch_assoc($rs2);
        $from_userid = $r2['userId'];

        if (isset($_GET["userid"])) {
            $userId = mysqli_real_escape_string($conn, $_GET["userid"]);
            $rs = mysqli_query($conn, "SELECT * FROM user WHERE userId='$userId'");
            if ($r = mysqli_fetch_array($rs)) {


                $updateMessages = "UPDATE message SET is_read = 1, read_status = 1 WHERE sender_userid = '$userId' AND receiver_userid = '$from_userid' AND is_read = 0";
                mysqli_query($conn, $updateMessages);


                $resetNotification = "UPDATE notifications SET unread_messages = 0 WHERE sender_userid = '$userId' AND receiver_userid = '$from_userid'";
                mysqli_query($conn, $resetNotification);

?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Chatify</title>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                    <style>
                        body {
                            background-color: #f0f8ff;
                            font-family: Arial, sans-serif;
                        }

                        .background-image {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            overflow: hidden;
                            z-index: -1;
                        }

                        #image-background {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }

                        .user-name {
                            color: #FF9800;
                            font-weight: bold;
                            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
                        }

                        .overlay {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 0, 0.6);
                            opacity: 0.8;
                            transition: opacity 0.5s ease;
                            z-index: -1;
                        }
                        
                        .single-tick::after {
                            content: '✔';
                            color: gray;
                            margin-left: 5px;
                        }

                        .blue-tick::after {
                            content: '✔✔'; 
                            color: blue; 
                            margin-left: 5px;
                        }

                        .card {
                            width: 100%;
                            max-width: 800px;
                            margin: auto;
                            border-radius: 10px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                            padding: 20px;
                            box-shadow: 1px 1px 20px #02cefcf7, 1px 1px 60px #ff6b6bf7;
                        }

                        .user-info {
                            text-align: center;
                            margin-bottom: 20px;
                        }

                        .user-info img {
                            border-radius: 50%;
                            width: 80px;
                            height: 80px;
                            object-fit: cover;
                        }

                        .status-indicator {
                            font-weight: bold;
                            margin-left: 10px;
                        }

                        .chat-box {
                            min-height: 300px;
                            max-height: 300px;
                            overflow-y: auto;
                            padding: 10px;
                            background: #f7f7f7;
                            box-shadow: inset 0 32px 32px -32px rgba(0, 0, 0, 0.05),
                                inset 0 -32px 32px -32px rgba(0, 0, 0, 0.05);
                        }

                        .chat-message {
                            margin: 10px 0;
                        }

                        .outgoing {
                            display: flex;
                            justify-content: flex-end;
                            margin: 10px 0;
                        }

                        .outgoing p {
                            background: #333;
                            color: #fff;
                            border-radius: 18px 18px 0 18px;
                            padding: 8px 16px;
                            word-wrap: break-word;
                            display: inline-block;
                            max-width: 60%;
                            white-space: normal;
                            text-align: left;
                        }

                        .incoming {
                            display: flex;
                            justify-content: flex-start;
                            margin: 10px 0;
                        }

                        .incoming p {
                            background: #fff;
                            color: #333;
                            border-radius: 18px 18px 18px 0;
                            padding: 8px 16px;
                            word-wrap: break-word;
                            display: inline-block;
                            max-width: 60%;
                            white-space: normal;
                            text-align: left;
                        }

                        .message-input {
                            display: flex;
                            margin-top: 10px;
                        }

                        .message-input input {
                            flex: 1;
                            padding: 8px;
                            border: 1px solid #ddd;
                            border-radius: 5px;
                        }

                        .message-input button {
                            background-color: #007bff;
                            border: none;
                            color: white;
                            padding: 10px;
                            border-radius: 5px;
                            cursor: pointer;
                        }
                    </style>
                </head>

                <body>
                    <div class="background-image">
                        <img src="image/logo.png" id="image-background">
                    </div>
                    <div class="container-fluid">
                        <div class="row justify-content-center" style="margin-top: 90px;">
                            <div class="col-12 col-md-8 col-lg-6">
                                <div class="card">
                                    <div class="user-info">
                                        <img src="images/<?php echo $userId ?>.jpg" alt="User Image">
                                        <div>
                                            <span class="user-name"><?php echo $r["first_name"] . " " . $r["last_name"]; ?></span>
                                            <span class="status-indicator" style="color: <?php echo $r["status"] ? 'green' : 'red'; ?>">
                                                ● <?php echo $r["status"] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>

                                        <button class="btn btn-primary">
                                            <a href="logout.php" style="text-decoration: none; color: red;">Logout</a>
                                        </button>
                                        <button class="btn btn-primary">
                                            <a href="Dashboard.php" style="text-decoration: none; color: blue;">Home</a>
                                        </button>
                                    </div>
                                    <hr>
                                    <div class="chat-box" id="chat-box"></div>
                                    <div class="message-input">
                                        <input type="text" id="message" placeholder="Type a message here..." autocomplete="off">
                                        <button class="send-btn">
                                            <i class="fa-brands fa-telegram"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="overlay"></div>
                            </div>
                        </div>
                    </div>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            
                            function loadData() {
                                var senderid = "<?php echo $from_userid; ?>";
                                var receiverid = "<?php echo $userId; ?>";
                                var email = "<?php echo $email; ?>";
                                var session = "<?php echo $session; ?>";

                                $.ajax({
                                    url: "get_chat.php",
                                    type: "POST",
                                    data: {
                                        senderid: senderid,
                                        receiverid: receiverid,
                                        email: email,
                                        session: session
                                    },
                                    success: function(data) {
                                        $("#chat-box").html(data);
                                        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
                                    }
                                });
                            }

                            function sendMessage() {
                                var msg = $("#message").val();
                                var userid = "<?php echo $userId; ?>";

                                $.ajax({
                                    url: "message.php",
                                    type: "POST",
                                    data: {
                                        msg: msg,
                                        userid: userid
                                    },
                                    success: function(data) {
                                        if (data.trim() == "success") {
                                            loadData();
                                            $("#message").val('');
                                        }
                                    }
                                });
                            }

                            $(".send-btn").click(function() {
                                sendMessage();
                            });

                            $("#message").keypress(function(e) {
                                if (e.which == 13) {
                                    sendMessage();
                                }
                            });

                            loadData();
                            setInterval(loadData, 3000);
                        });

                        setInterval(function() {
                            location.reload();
                        }, 30000);
                    </script>
                </body>

                </html>
<?php
            } else {
                echo "User not found.";
            }
        } else {
            echo "User ID not specified.";
        }

        mysqli_close($conn);
    } else {
        echo "You are not logged in.";
    }
} else {
    header("location: index.php");
    exit();
}
?>