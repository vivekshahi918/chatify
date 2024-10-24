<?php
session_start();
if (isset($_COOKIE["login"]) && isset($_SESSION["session"])) {
    $conn = mysqli_connect("localhost", "root", "", "chat_db");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($conn, $_COOKIE["login"]);
    $session = $_SESSION["session"];
    $rs2 = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    $r2 = mysqli_fetch_assoc($rs2);
    $from_userid = $r2['userId'];

    if (isset($_GET["userid"])) {
        $userId = mysqli_real_escape_string($conn, $_GET["userid"]);
        $rs = mysqli_query($conn, "SELECT * FROM user WHERE userId='$userId'");
        if ($r = mysqli_fetch_array($rs)) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Chatify</title>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                    <style>
                        hr {
                            border: 5px solid green;
                            border-radius: 2px;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                            overflow: hidden;
                        }
                        .background-video {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            overflow: hidden;
                            z-index: -2;
                        }
                        #video-background {
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            opacity: 0.4;
                        }
                        .blue-overlay {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 255, 0.3);
                            z-index: -1;
                        }
                        #message{
                            height:25px
                        }
                        #chat-box .chat img {
                            max-width: 100%;
                            height: auto;
                            border-radius: 10px;
                            margin: 5px 0;
                        }

                        .chat-box-container {
                            border-radius: 10px;
                            padding: 20px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        }
                        #chat-box {
                            position: relative;
                            min-height: 300px;
                            max-height: 300px;
                            overflow-y: auto;
                            padding: 10px 30px 20px 30px;
                            background: #f7f7f7;
                            box-shadow: inset 0 32px 32px -32px rgba(0, 0, 0, 0.05),
                                        inset 0 -32px 32px -32px rgba(0, 0, 0, 0.05);   
                        }
                        #chat-box .chat {
                            margin: 15px 0;
                        }
                        #chat-box .chat p {
                            word-wrap: break-word;
                            padding: 8px 16px;
                            box-shadow: 0 0 32px rgb(0, 0, 0, 0.08),
                                        0 16px 16px -16px rgb(0, 0, 0, 0.10);
                        }
                        #chat-box .outgoing {
                            display: flex;
                        }
                        .outgoing .details {
                            margin-left: auto;
                            max-width: calc(100% - 130px);
                        }
                        .outgoing .details p {
                            background: #333;
                            color: #fff;
                            border-radius: 18px 18px 0 18px;
                        }
                        #chat-box .incoming {
                            display: flex;
                            align-items: flex-end;
                        }
                        #chat-box .incoming img {
                            height: 35px;
                            width: 35px;
                        }
                        .incoming .details {
                            margin-left: 10px;
                            margin-right: auto;
                            max-width: calc(100% - 130px);
                        }
                        .incoming .details p {
                            background: #fff;
                            color: #333;
                            border-radius: 18px 18px 18px 0;
                        }
                        .send-btn {
                            background-color: #007bff;
                            border: none;
                            color: white;
                            padding: 10px 10px;
                            border-radius: 5px;
                            font-size: 18px;
                            cursor: pointer;
                        }
                        .rounded-file-icon, .rounded-camera-icon, .rounded-image-icon, .rounded-volume-icon, .rounded-map-icon, .rounded-address-icon {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            width: 60px;
                            height: 60px;
                            border-radius: 50%;
                            color: #fff;
                            font-size: 24px;
                            cursor: pointer;
                        }
                        .rounded-file-icon { background-color: #007bff; }
                        .rounded-camera-icon { background-color: #FFA500; }
                        .rounded-image-icon { background-color: #A52A2A; }
                        .rounded-volume-icon { background-color: #00FF00; }
                        .rounded-map-icon { background-color: #808080; }
                        .rounded-address-icon { background-color: #0000FF; }
                        .icon {
                            display: none;
                        }
                        .fa.fa-edit, .fa.fa-trash {
                            color: blue;
                            font-size: 15px;
                            cursor: pointer;
                        }
                    </style>
                </head>
                <body>
                    <div class="background-video">
                        <video autoplay muted loop id="video-background">
                            <source src="videos/background4.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div class="blue-overlay"></div>
                    <div class="chat-box-container">
                        <div class="container-fluid">
                            <div class="row" style="margin-top: 130px;">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td align="center">
                                                            <img src="images/<?php echo $userId ?>.jpg" class="rounded-circle" style="width: 80px; height: 80px;border-radius: 50%; object-fit: cover;">
                                                            <div><?php echo $r["first_name"] . " " . $r["last_name"]; ?>
                                                            <?php if ($r["status"] == 1) { ?>
                                                                <span style="color: green; font-weight: bold; margin-left: 10px;">●</span>
                                                            <?php } ?>
                                                         </div>
                                                        </td>
                                                        
                                                        <td>
                                                            <button class="btn btn-primary">
                                                                <a href="logout.php" style="text-decoration: none; color: red;">Logout</a>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-primary">
                                                                <a href="Dashboard.php" style="text-decoration: none; color: blue;">Home</a>
                                                            </button>
                                                        </td>
                                    
                                                    </tr>
                                                </table>
                                            </div>
                                            <hr>
                                            <div id="chat-box"></div>
                                            <div class="row">
                                                <input type="text" id="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
                                                <button class="send-btn">
                                                    <i class="fa-brands fa-telegram"></i>
                                                </button>
                                                <button id="openFileInputBtn">
                                                    <i class="fa fa-paperclip"></i>
                                                </button>
                                                <input type="file" id="fileInput" accept="image/*,video/*" style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                        </div>
                    </div>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                              $(document).ready(function() {
                                var isScrolledToBottom = true; // Track if the user is at the bottom of the chat

                                function loadData() {
                                    var senderid = "<?php echo $from_userid; ?>";
                                    var receiverid = "<?php echo $userId; ?>";
                                    var email = "<?php echo $email; ?>";
                                    var session = "<?php echo $session; ?>";

                                    $.ajax({
                                        url: "get_chat.php",
                                        type: "POST",
                                        data: { senderid: senderid, receiverid: receiverid, email: email, session: session },
                                        success: function(data) {
                                            $("#chat-box").html(data);

                                            // Scroll to bottom if user was already at the bottom
                                            if (isScrolledToBottom) {
                                                scrollToBottom();
                                            }
                                        }
                                    });
                                }
                                function scrollToBottom() {
                                    var chatBox = document.getElementById("chat-box");
                                    chatBox.scrollTop = chatBox.scrollHeight;
                                }

                                $("#chat-box").on("scroll", function() {
                                    var chatBox = this;
                                    var isAtBottom = chatBox.scrollHeight - chatBox.scrollTop === chatBox.clientHeight;
                                    isScrolledToBottom = isAtBottom;
                                });

                                function sendMessage() {
                                    var msg = $("#message").val();
                                    var userid = "<?php echo $userId; ?>";
                                    var fileInput = $("#fileInput")[0].files[0];

                                    var formData = new FormData();
                                    formData.append("msg", msg);
                                    formData.append("userid", userid);
                                    if (fileInput) {
                                        formData.append("file", fileInput);
                                    }

                                    $.ajax({
                                        url: "message.php",
                                        type: "POST",
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        success: function(data) {
                                            if (data.trim() == "success") {
                                                loadData(); // Reload data after sending a message
                                                $("#message").val(''); // Clear the input field
                                                $("#fileInput").val(''); // Clear file input

                                                // Scroll to the bottom if the user was not at the bottom before sending a message
                                                setTimeout(function() {
                                                    if (!isScrolledToBottom) {
                                                        scrollToBottom();
                                                    }
                                                }, 50); // Timeout to ensure chat updates before scrolling
                                            }
                                        }
                                    });
                                }

                                $(".send-btn").click(function() {
                                    sendMessage();
                                });

                                $("#message").keypress(function(e) {
                                    if (e.which == 13) { // Enter key
                                        sendMessage();
                                    }
                                });

                                $("#openFileInputBtn").click(function() {
                                    $("#fileInput").click();
                                });

                                $("#fileInput").change(function() {
                                    // Handle file change if necessary
                                });

                                // Initial load of chat data
                                loadData(); 

                                // Periodic reload of chat data
                                setInterval(loadData, 500);
                            });

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
?>
