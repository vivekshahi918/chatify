<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "chat_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is already logged in and not trying to register
if (isset($_SESSION["session"]) && !isset($_GET['signup'])) {
    header("location: Dashboard.php"); // Redirect to dashboard if logged in and not on signup page
    exit();
}

// Determine whether to show the login or signup form based on a URL parameter (e.g., index.php?signup=true)
$show_signup_form = isset($_GET['signup']);
?>
<?php include_once "header.php" ?>
<style>
    body {
        background-color: lightblue;
    }
</style>
<body>
<div class="container-fluid">
    <div class="row" style="margin-top:60px;">
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    <h3>Chatify</h3>
                </div>
                <div class="card-body">

                    <?php if ($show_signup_form) { ?>
                        <!-- Registration Form -->
                        <form method="post" enctype="multipart/form-data" action="insert_record.php">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php
                                    // Display registration errors
                                    $errors = [
                                        'empty' => 'All Fields Are Required',
                                        'invalid_name' => 'Please Enter Your Valid Name',
                                        'invalid_email' => 'Please Enter Your Valid Email-Id',
                                        'already' => 'Account Already Registered',
                                        'record_inserted' => 'Registration Successful',
                                        'again' => 'Try Again',
                                        'img_err' => 'Image Uploading Error',
                                        'invalid_password' => 'Please Enter Valid Password'
                                    ];
                                    
                                    foreach ($errors as $key => $message) {
                                        if (isset($_GET[$key])) {
                                            echo "<div class='alert alert-" . ($key === 'record_inserted' ? 'success' : 'danger') . "'>$message</div>";
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <label>First Name</label>
                                    <input type="text" name="fname" placeholder="First Name" required class="form-control">
                                </div>
                                <div class="col-sm-6">
                                    <label>Last Name</label>
                                    <input type="text" name="lname" placeholder="Last Name" required class="form-control">
                                </div>
                            </div>
                            <div class="row" style="margin-top:15px;">
                                <label>Email-Id</label>
                                <input type="email" name="email" placeholder="Email-Id" required class="form-control">
                            </div>
                            <div class="row" style="margin-top:15px;">
                                <label>Password</label>
                                <input type="password" name="pass" placeholder="Password" required class="form-control">
                            </div>
                            <div class="row" style="margin-top:15px;">
                                <label>Select Image</label>
                                <input type="file" name="photo" required class="form-control">
                            </div>
                            <div class="row" style="margin-top:15px;">
                                <button class="btn btn-info form-control" type="submit">Sign Up</button>
                            </div>
                        </form>
                        <div class="row" style="margin-top:25px;">
                            <h5>Already Signed Up?</h5><a href="index.php"><strong>Login Here</strong></a>
                        </div>
                    <?php } else { ?>
                        <!-- Login Form -->
                        <form method="post" action="check.php">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php
                                    if (isset($_GET["invalid_credentials"])) {
                                        echo '<div class="alert alert-danger">Invalid Email or Password</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row" style="margin-top:15px;">
                                <label>Email-Id</label>
                                <input type="email" name="email" placeholder="Email-Id" required class="form-control">
                            </div>
                            <div class="row" style="margin-top:15px;">
                                <label>Password</label>
                                <input type="password" name="pass" placeholder="Password" required class="form-control">
                            </div>
                            <div class="row" style="margin-top:15px;">
                                <button class="btn btn-info form-control" type="submit">Login</button>
                            </div>
                        </form>
                        <div class="row" style="margin-top:25px;">
                            <h5>New User?</h5><a href="index.php?signup=true"><strong>Signup Now</strong></a>
                        </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
