<?php
session_start();
if(isset($_COOKIE["login"]) && isset($_SESSION["session"])){
    $email = $_COOKIE["login"];
    $session = $_SESSION["session"];
    $conn = mysqli_connect("localhost", "root", "", "chat_db");
    $rs = mysqli_query($conn, "SELECT * FROM user WHERE email ='$email'");
    if($r = mysqli_fetch_array($rs)){
        ?>
        <?php include_once "header.php"; ?>
        <style>
            body {
                background-color: #f0f8ff; /* Light Alice Blue */
                font-family: Arial, sans-serif;
            }
            hr {
                border: 5px solid #4caf50; /* Green */
                border-radius: 2px;
            }
            .background-video {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                z-index: -1; /* Ensure the video is behind the content */
            }

            #video-background {
                width: 100%;
                height: 100%;
                object-fit: cover; /* Ensure the video covers the entire background */
            }

            .card {
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                transition: box-shadow 0.3s ease-in-out;
            }
            .card:hover {
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
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
            $(document).ready(function(){
                $("input#Search").keyup(function(){
                    var searchValue = $(this).val();
                    var email = "<?php echo $email; ?>";
                    if(searchValue === ""){
                        $.post("new_dashboard.php", {email: email}, function(data){
                            $("#record").html(data);
                        });
                    } else {
                        $.post("search.php", {ch: searchValue}, function(data){
                            $("#record").html(data);
                        });
                    }
                });

                $(document).on("click", ".chat-link", function() {
                    window.location.href = $(this).data("href");
                });
            });
        </script>
        <body>
        <div class="background-video">
        <video autoplay muted loop id="video-background">
            <source src="videos/background1.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
            <div class="container-fluid">
                <div class="row" style="margin-top:130px;">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4">
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
                                                <?php if ($r["status"] == 1) { ?>
                                                    <br><strong>Active Now</strong>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary">
                                                    <a href="logout.php" style="text-decoration:none;color:white">Logout</a>
                                                </button>
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
                                    $rp = mysqli_query($conn, "SELECT * FROM user WHERE email<>'$email'");
                                    echo "<table class='table table-borderless'>";
                                    while ($rn = mysqli_fetch_array($rp)) {
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="#" class="chat-link" data-href="chat.php?userid=<?php echo $rn["userId"]; ?>">
                                                    <img src="images/<?php echo $rn["userId"]; ?>.jpg" class="rounded-circle" style="width:60px;height:60px;">
                                                    <span><?php echo $rn["first_name"] . " " . $rn["last_name"]; ?></span>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    echo "</table>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
} else {
    header("location:login.php");
}
?>
