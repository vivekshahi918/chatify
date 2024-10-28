<?php 
include_once "header.php";
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Open Sans', sans-serif;
    }
    body {
        background-color: #111;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 95vh;
        width: 100%;
        max-width: 1000px;
        height: 350px;
        background: #222;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        z-index: 0;
    }
    .left-side {
        margin-top: 16px;
        width: 50%;
        margin-right: 20%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
    }
    .left-side h3 {
        font-size: 30px;
        margin-bottom: 10px;
    }

    .square {
        position: absolute;
        width: 400px;
        height: 500px;
        top: 50%;
        left: 75%;
        transform: translate(-50%, -50%);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: -1;
    }
    .square i {
        position: absolute;
        inset: 0;
        border: 2px solid var(--clr);
        border-radius: 50%;
        transition: 0.5s;
    }
    .glow .square i {
        border: 4px solid var(--clr);
        box-shadow: 0 0 15px var(--clr), 0 0 30px var(--clr);
    }
    .black .square i {
        border: 2px solid #000;
        box-shadow: none;
    }

    .square i:nth-child(1) {
        animation: rotate 6s linear infinite;
    }
    .square i:nth-child(2) {
        animation: rotate 4s linear infinite reverse;
        --clr: #0000ff;
    }
    .square i:nth-child(3) {
        animation: rotate 8s linear infinite;
        --clr: #ff00ff;
    }
    .square i:nth-child(5) {
        animation: rotate 12s linear infinite;
        --clr: #ff0000;
    }
    .square i:nth-child(6) {
        animation: rotate 5s linear infinite;
        --clr: #fff000;
    }
    .square i:nth-child(4) {
        animation: rotate 10s linear infinite reverse;
        --clr: #800000;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .card {
        width: 35%;
        height: auto;
        margin-right: 10%;
        background: rgba(0, 0, 0, 0.7);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        color: #fff;
        text-align: center;
        position: relative;
        z-index: 1;
        margin-top: 0;
    }
    .right-side.card {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .card h2 {
        margin-bottom: 20px;
        font-size: 1.6em;
    }
    .inputBx {
        margin-bottom: 15px;
    }
    .inputBx input[type="email"],
    .inputBx input[type="password"] {
        width: 90%;
        padding: 8px;
        margin-top: 5px;
        border: none;
        border-radius: 5px;
        font-size: 10px;
    }
    .inputBx input[type="submit"] {
        background-color: #ff0057;
        border: none;
        padding: 8px;
        width: 90%;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        font-size: 10px;
    }
    .links {
        margin-top: 10px;
        font-size: 0.9em;
    }
    .links a {
        color: #fffd44;
        text-decoration: none;
    }
    .links a:hover {
        text-decoration: underline;
    }
</style>

<body>
<div class="container">
    <div class="left-side">
        <img src="./image/logo.png" alt="Chatify Logo" style="width: 300px; height: 300px; margin-bottom: 20px;">
        <h3>Welcome to Chatify</h3>
        <p>Your secure and friendly chat platform.</p>
    </div>
    <div id="login" class="right-side card">
        <h2>Login</h2>
        <form method="post" action="check.php">
            <?php if(isset($_GET["empty"])): ?>
                <div class="alert alert-danger">All Fields Are Required</div>
            <?php elseif(isset($_GET["invalid_password"])): ?>
                <div class="alert alert-warning">Password Mismatch</div>
            <?php elseif(isset($_GET["invalid_record"])): ?>
                <div class="alert alert-info">Please Create Account</div>
            <?php endif; ?>
            
            <div class="inputBx"><input type="email" name="email" placeholder="Email-Id" required></div>
            <div class="inputBx"><input type="password" name="pass" placeholder="Password" required></div>
            <div class="inputBx"><input type="submit" value="Continue to Chat"></div>
            <div class="links"><p>New User? <a href="index.php?signup=true">Signup Now</a></p></div>
        </form>
    </div>
    <div class="square">
        <i></i>
        <i></i>
        <i></i>
        <i></i>
        <i></i>
        <i></i>
    </div>
</div>

<script>
    const welcome = document.querySelector(".left-side");
    const login = document.querySelector(".right-side");
    const container = document.querySelector(".container");

    welcome.addEventListener("mouseenter", () => {
        container.classList.add("black");
        container.classList.remove("glow");
    });

    login.addEventListener("mouseenter", () => {
        container.classList.add("glow");
        container.classList.remove("black");
    });

    container.addEventListener("mouseleave", () => {
        container.classList.remove("black");
        container.classList.remove("glow");
    });
</script>
</body>
</html>
