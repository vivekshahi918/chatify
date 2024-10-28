<?php
if (isset($_REQUEST["email"])) {
    $conn = mysqli_connect("localhost", "root", "", "chat_db");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($conn, $_REQUEST["email"]);
    
    // Fetch users excluding the logged-in user, along with their activity status
    $query = "
        SELECT *, 
        (CASE WHEN last_activity <> logout_time THEN 1 ELSE 0 END) AS status 
        FROM user 
        WHERE email != '$email'";
        
    $rs = mysqli_query($conn, $query);

    echo "<table class='table table-borderless'>";
    
    while ($r = mysqli_fetch_array($rs)) {
        // Determine if the user is active or inactive
        $statusIndicator = $r['status'] == 1 
            ? "<span style='color: green;'>● Active</span>" 
            : "<span style='color: red;'>● Inactive</span>";
        ?> 
        <tr>
            <td>
                <a href="chat.php?userid=<?php echo $r["userId"]; ?>" style="text-decoration:none;color:black;">
                    <img src="images/<?php echo $r["userId"]; ?>.jpg" class="rounded-circle" style="width:60px;height:60px;">
                    <span><?php echo $r["first_name"] . " " . $r["last_name"]; ?></span>
                    <?php echo $statusIndicator; ?>
                    <span><p>message....</p></span>
                </a>
            </td>
        </tr>
        <?php
    }
    echo "</table>";

    mysqli_close($conn);
}
?>
